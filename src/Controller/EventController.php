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
use App\Entity\User;
use App\Form\EventType;
use App\Form\UserListType;
use App\Remote\AssoManager;
use App\Remote\UserRemoteManager;
use App\Repository\AvailabilityRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class EventController extends AbstractController
{
    public function show(Event $event)
    {
        return $this->render('event/summary.html.twig', [
            'event' => $event,
        ]);
    }

    public function resources(Event $event, Request $request, AvailabilityRepository $availabilityRepository, EntityManagerInterface $em, UserRemoteManager $userRemoteManager)
    {
        if ($request->query->has('field_name')) {
            dump($userRemoteManager->findAll());
            return $this->json([
                [
                    'id' => 'dddaab',
                    'text' => 'Corentin HEMBISE'
                ],[
                    'id' => 'dddaac',
                    'text' => 'Alfred HICHOFA'
                ],[
                    'id' => 'dddaad',
                    'text' => 'Renand Lute'
                ],[
                    'id' => 'dddaae',
                    'text' => 'Camille Jouarie'
                ],[
                    'id' => 'dddaaf',
                    'text' => 'Albert CAMUS',
                    "disabled"=> true
                ],
            ]);
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

        $invitationForm = $this->createFormBuilder()
            ->add('users', Select2EntityType::class, [
                'label' => 'Selectionner des utilisateur·ice·s',
                'help' => "Il n'est possible d'inviter que des utilisateurs disposant un compte sur le portail des assos.",
                'remote_route' => 'index',
                'placeholder' => 'Rechercher un utilisateur·ice·s du portail',
                'multiple' => true,
            ])
            ->add('message', TextareaType::class, [
                'label' => "Message d'invitation",
            ])
            ->getForm()
        ;

        return $this->render('event/ressources/list.html.twig', [
            'event' => $event,
            'removeForm' => $removeForm->createView(),
            'invitationForm' => $invitationForm->createView(),
            'groupForm' => $groupForm->createView(),
        ]);
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
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($jsonArray, 200, [], true);
    }

    public function invitations()
    {
        return $this->render('event/ressources/invitations.html.twig');
    }

    public function edit(Event $event, Request $request)
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
                $em = $this->getDoctrine()->getManager();
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

    public function new(AssoManager $assoManager)
    {
        $assos = $assoManager->findByUserWithPermissions($this->getUser(), "69d6a120-5345-11e9-8edd-43c9792c1d4a");

        $form = $this->createForm(EventType::class, null, [
            'organizations' => $assos
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'Créer',
        ]);

        return $this->render('event/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function planning(Event $event, AssoManager $assoManager)
    {
        $asso = $assoManager->find("6a8bc6e0-5345-11e9-aff8-bd263b9b07f9");
        dump($asso);
        $assos = $assoManager->findBy([
            'login'=>"simde",
        ]);
        dump($assos);

        return $this->render('event/planning.html.twig', [
            'event' => $event,
        ]);
    }
}