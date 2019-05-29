<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-28
 */

namespace App\Controller;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationController extends AbstractController
{
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('portal_assos') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                '*', // the scopes you want to access
            ])
            ;
    }

    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)
/*
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client
        $client = $clientRegistry->getClient('portal_assos');

        try {
            // the exact class depends on which provider you're using
            $accessToken = $client->getAccessToken();
            $user = $client->fetchUserFromToken($accessToken);

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            dump($user); die;

            // ...
        } catch (IdentityProviderException $e) {
            throw $e;
            // something went wrong!
            // probably you should return the reason to the user
            dump($e->getMessage()); die;
        }
        */
    }

    public function logout()
    {
        return $this->redirectToRoute("index");
    }
}