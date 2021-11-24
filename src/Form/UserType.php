<?php

namespace App\Form;

use App\Entity\User;
use PhpParser\Node\Expr\Instanceof_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label' => "Nom d'utilisateur"])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
        ;
        // creation du bouton qui autorise à l'administrateur à basculer un user en admin
        
            
            $builder->add('isAdmin', CheckboxType::class, [
                'label'    => 'Administrateur',
                'mapped' => false,
                'required' => false,
                'data' => $builder->getData() Instanceof User ? in_array("ROLE_ADMIN", $builder->getData()->getRoles()) : false,
                'disabled' => (
                    ($builder->getData()->getUsername() == $this->security->getUser()->getUsername()) 
                    && (in_array("ROLE_ADMIN", $this->security->getUser()->getRoles()))) ? true : false
                ])
            ;
        
        
    }
}
