<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-20
 */

namespace App\Security;


use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    const ATTRIBUTES = [
        'manage', // S'il a la permission 'planner' sur l'asso en question
        'new',    // S'il a la permission 'planner' sur au moins une asso
        'view',   // S'il est dans la liste des participants
    ];

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!$subject instanceof Event) {
            return false;
        }

        if (in_array($attribute, self::ATTRIBUTES)) {
            return false;
        }
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param Event $event
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $event, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $orgaId = $event->getRemoteOrganizationId();
        $userId = $user->getPortalId();

    }
}