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
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class PortalAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, SessionInterface $session)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->session = $session;
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
        return $this->fetchAccessToken($this->getPortalClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $portalUser = $this->getPortalClient()
            ->fetchUserFromToken($credentials);



        $this->session->set('access_token', $credentials);

        return $portalUser;

        $user = $userProvider->loadUserByUsername($portalUser['id']);

        return $user;

        $portalUser = $portalUser->toArray();
        $user = new User();
        $user->setName($portalUser['name']);
        $user->setEmail($portalUser['email']);
        $user->setPortalId($portalUser['id']);
        $user->setEmail($portalUser['email']);
        $user->setFirstname($portalUser['firstname']);
        $user->setLastname($portalUser['lastname']);
        $user->setImage($portalUser['image']);
        $user->setIsActive($portalUser['is_active']);
        $user->setType($portalUser['type']);
        $user->setName($portalUser['name']);

        try {
            $user->setLastLoginAt(new \DateTime($portalUser['last_login_at']));
        } catch (\Exception $e) {}
        try {
            $user->setUpdatedAt(new \DateTime($portalUser['updated_at']));
        } catch (\Exception $e) {}
        try {
            $user->setCreatedAt(new \DateTime($portalUser['created_at']));
        } catch (\Exception $e) {}

        return $user;

        return $userProvider->loadUserByUsername($portalUser['email']);
        /*
        $email = $portalUser->getEmail();

        // 1) have they logged in with Facebook before? Easy!
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['facebookId' => $portalUser->getId()]);
        if ($existingUser) {
            return $existingUser;
        }

        // 2) do we have a matching user by email?
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        // 3) Maybe you just want to "register" them by creating
        // a User object
        $user->setFacebookId($portalUser->getId());
        $this->em->persist($user);
        $this->em->flush();
*/
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
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/auth/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    // ...
}