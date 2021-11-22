<?php
namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class TaskDataFixtures extends Fixture
{
   
    public function load(ObjectManager $manager)
    {
        $task = new Task;
        $task->setTitle('titre1');
        $task->setContent('ceci est une tache a faire');
        $task->toggle(false);
        $task->setCreatedAt(new DateTime('now'));        
        $manager->persist($task);
        $manager->flush();
    }
}