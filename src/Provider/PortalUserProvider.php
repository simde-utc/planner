<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-29
 */

namespace App\Provider;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
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

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(ClientRegistry $clientRegistry, UserRepository $userRepository)
    {
        $this->oAuthClient = $clientRegistry->getClient('portal_assos');
        $this->httpClient = new Client();
        $this->clientRegistry = $clientRegistry;
        $this->userRepository = $userRepository;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $remoteId The portalId
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws IdentityProviderException
     */
    public function loadUserByUsername($remoteId)
    {
        $existingUser = $this->userRepository->findByRemoteId($remoteId);

        if ($existingUser) {

        } else {

        }



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
        return $class == UserInterface::class;
    }
}