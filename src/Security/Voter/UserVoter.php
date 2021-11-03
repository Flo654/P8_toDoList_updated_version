<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter

{
    const CREATE = 'CAN_CREATE';
    const EDIT = 'CAN_EDIT';
    
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT]);            
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
            
        switch ($attribute) {
            case self::CREATE:
            //pour creer un compte utilisateur il faut, soit ne pas être connecté, soit être admin
            return(!$user instanceof User || $this->security->isGranted("ROLE_ADMIN"));
                
            case self::EDIT:
            //pour editer un compte , il faut etre admin
            return $this->security->isGranted("ROLE_ADMIN");                
        }

        return false;
    }
}
