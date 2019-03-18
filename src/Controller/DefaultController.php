<?php
/**
 * Created by PhpStorm.
 * User: corentinhembise
 * Date: 2019-03-16
 * Time: 18:44
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function index()
    {
        return $this->render('base.html.twig');
    }

    public function timeline()
    {
        return $this->render('timeline.html.twig');
    }
}