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

class PortalResourceOwner implements ResourceOwnerInterface
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
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}