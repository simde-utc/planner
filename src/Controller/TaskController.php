<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-26
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    public function tasks()
    {
        return $this->render('event/tasks/index.html.twig');
    }
}