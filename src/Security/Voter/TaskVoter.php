<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['TASK_EDIT', 'TASK_DELETE'])
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        $taskAuthor = $subject->getUser();
        if($user != 'anon.'){
            $isAdmin = in_array('ROLE_ADMIN', $user->getRoles())  ;
        }
       
        // if the user is anonymous, do not grant access
        
        ;   
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'TASK_EDIT':
                if ((!$taskAuthor && $isAdmin) || ($user === $taskAuthor)) {
                    return true;
                }
                break;
            case 'TASK_DELETE':
                if ((!$taskAuthor && $isAdmin) || ($user === $taskAuthor)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
