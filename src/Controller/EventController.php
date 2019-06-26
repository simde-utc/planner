<?php
/**
 * Created by PhpStorm.
 * User: corentinhembise
 * Date: 2019-03-19
 * Time: 10:31
 */

namespace App\Controller;


use App\Entity\Availability;
use App\Entity\Event;
use App\Entity\EquityGroup;
use App\Entity\EventRequest;
use App\Entity\User;
use App\Form\EquityGroupType;
use App\Form\EventType;
use App\Form\InvitationForm;
use App\Form\UserListType;
use App\Remote\AssoManager;
use App\Remote\UserRemoteManager;
use App\Repository\AvailabilityRepository;
use App\Repository\UserRepository;
use App\Repository\UserTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

//TODO: split this controller
class EventController extends AbstractController
{
    public function show(Event $event, UserTaskRepository $userTaskRepository)
    {
        $userTasks = $userTaskRepository->getUserTaskForEvent($event);
        return $this->render('event/summary.html.twig', [
            'event' => $event,
            'has_a_planning' => count($userTasks) > 0,
        ]);
    }

    public function resources(Event $event, Request $request, AvailabilityRepository $availabilityRepository, EntityManagerInterface $em, UserRemoteManager $userRemoteManager, SerializerInterface $serializer, UserRepository $userRepository)
    {
        if ($request->query->has('q')) {
            $users = $userRemoteManager->search($request->query->get('q'));

            return $this->json($users);
        }
        $removeForm = $this->createFormBuilder()
            ->add('users', UserListType::class, [
                'query_builder' => $availabilityRepository->createQbAllUsersForEvent($event),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Retirer',
            ])
            ->getForm()
        ;

        $groupForm = $this->createFormBuilder()
            ->add('users', UserListType::class, [
                'query_builder' => $availabilityRepository->createQbAllUsersForEvent($event),
            ])
            ->add('group', EntityType::class, [
                'label' => "Groupe d'équité",
                'class' => EquityGroup::class,
                'choices' => $event->getGroups(),
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Affecter',
            ])
            ->getForm()
        ;

        // Handle request for remove form and re
        $removeForm->handleRequest($request);
        $groupForm->handleRequest($request);

        if ($removeForm->isSubmitted() && $removeForm->isValid()) {
            $usersToRemove = $removeForm->get('users')->getData();
            foreach ($usersToRemove as $user) {
                $em->remove($user);
            }
            $em->flush();
        }

        if ($groupForm->isSubmitted() && $groupForm->isValid()) {
            $availabilitiesToChange = $groupForm->get('users')->getData();
            /** @var EquityGroup $group */
            $group = $groupForm->get('group')->getData();
            /** @var Availability $availability */
            foreach ($availabilitiesToChange as $availability) {
                $availability->setEquityGroup($group);
            }
            $em->flush();
        }

        $invitationForm = $this->createForm(InvitationForm::class);

        $invitationForm->handleRequest($request);
        if ($invitationForm->isSubmitted()) {
            /** @var User[] $users */
            $users = $invitationForm->get('users')->getData();

            foreach ($users as $user) {
                $availability = new Availability();
                $availability
                    ->setIsAvailable(null)
                    ->setUser($user)
                    ->setEvent($event)
                ;

                $em->persist($availability);
            }

            $em->flush();
        }

        return $this->render('event/ressources/list.html.twig', [
            'event' => $event,
            'removeForm' => $removeForm->createView(),
            'invitationForm' => $invitationForm->createView(),
            'groupForm' => $groupForm->createView(),
        ]);
    }

    public function resourcesAsso(Event $event, UserRemoteManager $userRemoteManager, UserRepository $userRepository)
    {
        //TODO: refactor this ugly code
        $remoteUsers = $userRemoteManager->findUsersForAsso($event->getRemoteOrganizationId());

        // Extract ids to search them in db
        $ids = array_map(function($remoteUser) {
            return $remoteUser->id;
        }, $remoteUsers);

        $qb = $userRepository->createQueryBuilder('u', 'u.remoteId');
        $qb
            ->innerJoin('u.availabilities', 'a')
            ->where($qb->expr()->in('u.remoteId', $ids))
            ->andWhere('a.event = :event')
            ->setParameter('event', $event->getId())
        ;
        /** @var User[] $invitedUsers */
        $invitedUsers = $qb->getQuery()->getResult();
        $idsInvitedUsers = array_keys($invitedUsers);
        $returnedUsers = [];
        foreach ($remoteUsers as $key => $remoteUser) {
            $returnedUsers[] = [
                'id' => $remoteUser->id,
                'text' => $remoteUser->name,
                'disabled' => in_array($remoteUser->id, $idsInvitedUsers),
            ];
        }

        return $this->json($returnedUsers);
    }

    /**
     * @ParamConverter("user", class="App\Entity\User",  options={"mapping": {"user_id": "id"}})
     */
    public function resourcesContact(Event $event, User $user, UserRemoteManager $userRemoteManager)
    {
        $contacts = $userRemoteManager->findContactFor($user->getRemoteId());
        return $this->json($contacts);
    }

    public function resourcesJson(Event $event, AvailabilityRepository $availabilityRepository, SerializerInterface $serializer)
    {
        $availabilities = $availabilityRepository->findAllUsersForEvent($event);

        $jsonArray = $serializer->serialize($availabilities, 'json', [
            'ignored_attributes' => [
                'event', 'availabilities', 'availability', 'skills', 'eventRequests',
            ],
        ]);

        return new JsonResponse($jsonArray, 200, [], true);
    }

    public function resourcesRequests(Event $event)
    {
        return $this->render('event/ressources/requests.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @param Event $event
     * @param EventRequest $eventRequest
     * @ParamConverter("eventRequest", class="App\Entity\EventRequest",  options={"mapping": {"user_id": "user", "id": "event"}})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resourcesRequestsAccept(Event $event, EventRequest $eventRequest)
    {
        return $this->resourcesRequestsAcceptOrRefuse($event, $eventRequest, true);
    }

    /**
     * @param Event $event
     * @param EventRequest $eventRequest
     * @ParamConverter("eventRequest", class="App\Entity\EventRequest",  options={"mapping": {"user_id": "user", "id": "event"}})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resourcesRequestsRefuse(Event $event, EventRequest $eventRequest)
    {
        return $this->resourcesRequestsAcceptOrRefuse($event, $eventRequest, false);
    }

    private function resourcesRequestsAcceptOrRefuse(Event $event, EventRequest $eventRequest, bool $accept)
    {
        $em = $this->getDoctrine()->getManager();
        if ($eventRequest->isAccepted() !== null) {
            throw new BadRequestHttpException("Impossible de modifier une candidature déjà acceptée ou refusée");
        }
        $eventRequest->setAccepted($accept);
        $eventRequest->setUpdatedAt(new \DateTime());
        $eventRequest->setModerator($this->getUser());
        // TODO: send a notification

        // Let's create a resource from the user
        if ($accept) {
            $availability = new Availability();
            $availability
                ->setEvent($event)
                ->setUser($eventRequest->getUser())
                ->setIsAvailable(true)
            ;
            $em->persist($availability);
        }

        $em->flush();

        return $this->redirectToRoute('event_resources_requests', [
            'id' => $event->getId(),
        ]);
    }

    public function resourcesEquityGroup(Event $event)
    {
        return $this->render('event/ressources/groups.html.twig', [
            'event' => $event,
        ]);
    }

    public function resourcesNewEquityGroup(Event $event, Request $request, EntityManagerInterface $em)
    {
        $group = new EquityGroup();
        $group->setEvent($event);

        $form = $this->createForm(EquityGroupType::class, $group)
            ->add('submit', SubmitType::class, [
                'label' => "Créer",
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($group);
            $em->flush();

            return $this->redirectToRoute('event_resources_groups', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/ressources/groups_new.html.twig', [
            'event' => $event,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("group", class="App\Entity\EquityGroup",  options={"mapping": {"group_id": "id"}})
     */
    public function resourcesEditEquityGroup(Event $event, EquityGroup $group, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(EquityGroupType::class, $group)
            ->add('submit', SubmitType::class, [
                'label' => "Enregistrer",
            ])
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('event_resources_groups', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/ressources/groups_edit.html.twig', [
            'event' => $event,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("group", class="App\Entity\EquityGroup",  options={"mapping": {"group_id": "id"}})
     */
    public function resourcesDeleteEquityGroup(Event $event, EquityGroup $group, EntityManagerInterface $em)
    {
        $em->remove($group);
        $em->flush();

        return $this->redirectToRoute('event_resources_groups', [
            'id' => $event->getId(),
        ]);
    }

    public function edit(Event $event, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(EventType::class, $event, [
            'disabled' => $event->isFinished(),
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
        ]);

        if (!$event->isFinished()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($event);
                $em->flush();
            }
        }


        return $this->render('event/settings/edit.html.twig', [
            'form'  => $form->createView(),
            'event' => $event,
        ]);
    }

    public function access()
    {
        return $this->render('event/settings/access.html.twig');
    }

    public function new(AssoManager $assoManager, Request $request, EntityManagerInterface $em)
    {
        $assos = $assoManager->findByUserWithPermissions($this->getUser(), "69d6a120-5345-11e9-8edd-43c9792c1d4a");

        $event = new Event();
        $form = $this->createForm(EventType::class, $event, [
            'organizations' => $assos
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'Créer',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_show', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function planning(Event $event, AssoManager $assoManager)
    {
        return $this->render('event/planner.html.twig', [
            'event' => $event,
        ]);
    }
}