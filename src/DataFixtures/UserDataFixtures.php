<?php
namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserDataFixtures extends Fixture
{
   
    public function load(ObjectManager $manager)
    {
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setPassword('admin');
        $adminUser->setEmail('admin@admin.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminUser);

        $user = new User();
        $user->setUsername('user');
        $user->setPassword('user');
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);       
        $manager->persist($user);
        
        $manager->flush();
    }
}