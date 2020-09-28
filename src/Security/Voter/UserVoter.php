<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['user_delete', 'user_update', 'user_view'])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $client = $token->getUser();
        if (!$client instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'user_delete':
            case 'user_update':
            case 'user_view':
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
