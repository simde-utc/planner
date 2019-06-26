<?php
/**
 * Created by PhpStorm.
 * User: corentinhembise
 * Date: 2019-03-16
 * Time: 18:44
 */

namespace App\Controller;

use App\PortalEntity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{

    public function index(ClientRegistry $clientRegistry)
    {
        /** @var AccessTokenInterface $accessToken */
        $accessToken = $this->get('session')->get('access_token');

        return $this->render('index.html.twig');
    }

    public function landing()
    {
        return $this->render('landing.html.twig');
    }

    public function timeline()
    {
        return $this->render('timeline.html.twig');
    }

    public function planning()
    {
        return $this->render('event/planning.html.twig');
    }
}