<?php
/**
 * Created by PhpStorm.
 * User: corentinhembise
 * Date: 2019-03-19
 * Time: 10:31
 */

namespace App\Controller;


use App\Entity\Event;
use App\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class EventController extends AbstractController
{
    public function show(Event $event)
    {
        return $this->render('event/summary.html.twig', [
            'event' => $event,
        ]);
    }

    public function resources(Event $event)
    {
        $users = [
            [
                "name" => "Mark Zuckzuck",
                "email" => "mzuckzuck@etu.utc.fr",
                "availability" => 0,
                "working_time" => new \DateTime("09:00:00"),
                "group" => null,
            ],
            [
                "name" => "Jacob Delafond",
                "email" => "jdelafond@etu.utc.fr",
                "availability" => 1,
                "working_time" => new \DateTime("09:00:00"),
                "group" => "Organisation",
            ],
            [
                "name" => "Romain Zuckzuck",
                "email" => "mzuckzuck@etu.utc.fr",
                "working_time" => null,
                "availability" => 2,
                "group" => "Bénévole",
            ],
            [
                "name" => "René Zuckzuck",
                "email" => "mzuckzuck@etu.utc.fr",
                "working_time" => new \DateTime("09:30:00"),
                "availability" => null,
                "group" => "Organisation",
            ]
        ];
        return $this->render('event/ressources/list.html.twig', [
            'event' => $event,
            'users' => $users,
        ]);
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

    public function new()
    {
        $form = $this->createForm(EventType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'Créer',
        ]);

        return $this->render('event/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}