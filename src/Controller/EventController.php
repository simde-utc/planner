<?php
/**
 * Created by PhpStorm.
 * User: corentinhembise
 * Date: 2019-03-19
 * Time: 10:31
 */

namespace App\Controller;


use App\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EventController extends AbstractController
{
    public function show()
    {
        return $this->render('event/summary.html.twig');
    }

    public function resources()
    {
        return $this->render('event/ressources/list.html.twig');
    }

    public function invitations()
    {
        return $this->render('event/ressources/invitations.html.twig');
    }

    public function edit()
    {
        $form = $this->createForm(EventType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
        ]);

        return $this->render('event/settings/edit.html.twig', [
            'form' => $form->createView()
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