<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-28
 */

namespace App\Provider;


use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PortalResourceOwner implements ResourceOwnerInterface, EquatableInterface, UserInterface
{

    /**
     * @var array
     */
    private $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId(): ?string
    {
        return $this->response['id'] ?: null;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->response['email'] ?: null;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->response['firstname'] ?: null;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->response['lastname'] ?: null;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->response['image'] ?: null;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->response['is_active'] ?: null;
    }

    /**
     * @return \DateTime
     */
    public function getLastLoginAt(): \DateTime
    {
        return $this->response['last_login_at'] ?: null;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->response['created_at'] ?: null;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->response['updated_at'] ?: null;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->response['type'] ?: null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->response['name'] ?: null;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof PortalResourceOwner) {
            return false;
        }

        if ($user->getId() != $this->getId()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }
}