<?php
/**
 * Created by
 * corentinhembise
 * 2019-04-09
 */

namespace App\Controller;


use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GroupController extends AbstractController
{
    public function index(Event $event)
    {
        return $this->render('event/groups/index.html.twig', [
            'event' => $event,
        ]);
    }
}