<?php
/**
 * Created by PhpStorm.
 * User: corentinhembise
 * Date: 2019-03-19
 * Time: 10:31
 */

namespace App\Controller;


use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Remote\AssoManager;
use App\Repository\AvailabilityRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class EventController extends AbstractController
{
    public function show(Event $event)
    {
        return $this->render('event/summary.html.twig', [
            'event' => $event,
        ]);
    }

    public function resources(Event $event, AvailabilityRepository $availabilityRepository)
    {
        $availabilities = $availabilityRepository->findAllUsersForEvent($event);

        return $this->render('event/ressources/list.html.twig', [
            'event' => $event,
            'availabilities' => $availabilities,
        ]);
    }

    /**
     * @ParamConverter("user", class="App\Entity\User",  options={"mapping": {"user_id": "id"}})
     */
    public function resourcesContact(Event $event, User $user)
    {
        return $this->json([
              [
                "id" => "c6fe46d0-57c5-11e9-9c74-2b0c4b0d2c5f",
                "name" => "Téléphone principal",
                "value" => "0629017973",
                "type" => [
                    "name" => "Numéro de téléphone",
                  "type" => "phone",
                  "pattern" => "^\\+?[0-9 \\.]*$"
                ],
                "visibility" => [
                    "id" => "699f9780-5345-11e9-9486-2daa3018b80b",
                  "type" => "public",
                  "name" => "Public"
                ]
              ]
        ]);
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