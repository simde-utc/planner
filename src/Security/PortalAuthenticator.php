<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-28
 */

namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Exception\InvalidStateException;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Exception\IdentityProviderAuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Exception\InvalidStateAuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Exception\NoAuthCodeAuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Helper\PreviousUrlHelper;
use KnpU\OAuth2ClientBundle\Security\Helper\SaveAuthFailureMessage;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Serializer\SerializerInterface;

class PortalAuthenticator extends AbstractGuardAuthenticator
{
    use PreviousUrlHelper;
    use SaveAuthFailureMessage;

    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, SessionInterface $session, SerializerInterface $serializer)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->session = $session;
        $this->serializer = $serializer;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_portal_check';
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array).
     *
     * Whatever value you return here will be passed to getUser() and checkCredentials()
     *
     * For example, for a form login, you might:
     *
     *      return [
     *          'username' => $request->request->get('_username'),
     *          'password' => $request->request->get('_password'),
     *      ];
     *
     * Or for an API token that's on a header, you might use:
     *
     *      return ['api_key' => $request->headers->get('X-API-TOKEN')];
     *
     * @param Request $request
     *
     * @return mixed Any non-null value
     *
     * @throws \UnexpectedValueException If null is returned
     */
    public function getCredentials(Request $request)
    {
        try {
            return $this->getPortalClient()->getAccessToken();
        } catch (MissingAuthorizationCodeException $e) {
            throw new NoAuthCodeAuthenticationException();
        } catch (IdentityProviderException $e) {
            throw new IdentityProviderAuthenticationException($e);
        } catch (InvalidStateException $e) {
            throw new InvalidStateAuthenticationException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Fetch remote user
        $remoteUser = $this->getPortalClient()->fetchUserFromToken($credentials);
        // Check if user exists locally, otherwise, create it
        try {
            $user = $userProvider->loadUserByUsername($remoteUser->getId());
        } catch (UsernameNotFoundException $e) {
            //$user = new User();
            //$this->serializer
            $remoteUser = $remoteUser->toArray();
            $user = new User();
            $user
                ->setEmail($remoteUser['email'])
                ->setFirstname($remoteUser["firstname"])
                ->setLastname($remoteUser["lastname"])
                ->setImage($remoteUser["image"])
            ;
            $this->em->persist($user);
        }

        $user->setLastLogin(new \DateTime());
        $this->em->flush();
        $this->session->set('access_token', $credentials);

        return $user;
    }

    /**
     * @return OAuth2Client
     */
    private function getPortalClient()
    {
        return $this->clientRegistry->getClient('portal_assos');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse('/');
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/auth/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // do nothing - the fact that the access token works is enough
        return true;
    }

    public function supportsRememberMe()
    {
        return true;
    }
}