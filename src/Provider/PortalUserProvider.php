<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-29
 */

namespace App\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class PortalUserProvider implements UserProviderInterface
{
    /**
     * @var \KnpU\OAuth2ClientBundle\Client\OAuth2Client
     */
    private $oAuthClient;

    /**
     * @var Client
     */
    private $httpClient;

    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->oAuthClient = $clientRegistry->getClient('portal_assos');
        $this->httpClient = new Client();
    }
    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $portalId The portalId
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($portalId)
    {
        $accessToken = $this->oAuthClient->getAccessToken();
        $request = new Request('GET', 'http://127.0.0.1/api/v1/user', [
            'access_token' => $accessToken
        ]);
        $response = $this->httpClient->request($request);

        $body = $response->getBody();
        dump($body);
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class == PortalResourceOwner::class;
    }
}