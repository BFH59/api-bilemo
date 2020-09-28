<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{

    const VIEW = 'user_view';
    const UPDATE = 'user_update';
    const DELETE = 'user_delete';

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $client = $token->getUser();
        if (!$client instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
            case self::UPDATE:
            case self::VIEW:
                return $this->isUserOwner($subject, $client);
                break;
        }

        return false;
    }

    /**
     * @param $subject
     * @param $client
     * @return bool
     */
    private function isUserOwner($subject, $client): bool
    {
        return $subject->getClient() === $client;
    }
}
