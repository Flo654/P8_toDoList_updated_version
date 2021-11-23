<?php
namespace App\DataFixtures;

use DateTime;
use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\UserDataFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class TaskDataFixtures extends Fixture implements DependentFixtureInterface
{
   
    
    public function load(ObjectManager $manager)
    {
        $admin = $manager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $user = $manager->getRepository(User::class)->findOneBy(['username'=>'user']);
                
        $task = new Task;
        $task->setTitle('tache1');
        $task->setContent('ceci est une tache a faire');
        $task->toggle(false);
        $task->setCreatedAt(new DateTime('now'));        
        $manager->persist($task);

        $task1 = new Task;
        $task1->setTitle('Courses');
        $task1->setContent('acheter du vin');
        $task1->setUser($admin);
        $task1->toggle(false);
        $task1->setCreatedAt(new DateTime('now'));        
        $manager->persist($task1);

        $task2 = new Task;
        $task2->setTitle('Administratif');
        $task2->setContent('resilier abonnement Canal+');
        $task2->setUser($user);
        $task2->toggle(false);
        $task2->setCreatedAt(new DateTime('now'));        
        $manager->persist($task2);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserDataFixtures::class
        ];
    }

}