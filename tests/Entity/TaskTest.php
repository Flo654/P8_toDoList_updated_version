<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use DateTime;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class TaskTest extends KernelTestCase
{
    public function getNewTask() :Task
    {
        
        $task = new Task();
        $task->setTitle('ma nouvelle tache');
        $task->setContent('ceci est une nouvelle tache à faire');
        
        return $task;
    }
    public function assertHasErrors(Task $task, int $number)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($task);            
        $this->assertCount($number, $errors);
    }

    public function testValidTaskEntity()
    {
        $this->assertHasErrors($this->getNewTask(), 0);
        
    }

    public function testNotOkIfTitleIsBlank(){
        $blankTitle = $this->getNewTask();
        $blankTitle->setTitle('');
        $this->assertHasErrors($blankTitle, 1);
    }
    
    public function testNotOkIfContentIsBlank(){
        $blankTitle = $this->getNewTask();
        $blankTitle->setContent('');
        $this->assertHasErrors($blankTitle, 1);
    }

    public function testDefaultIsDoneEqualsFalse()
    {
        $this->assertFalse($this->getNewTask()->isDone());
    }
        
}