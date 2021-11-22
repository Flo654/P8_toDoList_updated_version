<?php
namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\utils\LoginUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use LoginUser;

    private $client;
    private $objectManager;

    public function setUp():void
    {
        $this->client = static::createClient();
        $this->objectManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testTaskListIsUp()
    {

        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->logIn($user);
        
        $this->client->request('GET','/tasks');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'h1',
            'Liste des taches'
        );
    }

    public function testCreateTaskIsSuccessful()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'user']);
        $this->logIn($user);
        $crawler = $this->client->request('GET','/tasks/create');
        $this->assertSelectorExists('form');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'tache suivante',
            'task[content]' => 'ceci est a faire'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302);             
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();        
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été ajoutée');  
    }

    public function testResponseEditTaskIfNotTheOwner()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'user']);
        $this->logIn($user);
        //dd($user);
        $task = $this->objectManager->getRepository(Task::class)->findOneBy(['title'=>'titre1']);
        //dd($task);
        $crawler = $this->client->request('GET','/tasks/'.$task->getId().'/edit');
        
        $this->assertNotEquals($task->getUser(),$user->getUsername());
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode()); 
        
    }

    public function testResponseEditTaskIfAdminOrOwner()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->logIn($user);

        $task = $this->objectManager->getRepository(Task::class)->findOneBy(['title'=>'titre1']);
        $crawler = $this->client->request('GET','/tasks/'.$task->getId().'/edit');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'button',
            'Modifier'
        );  
    }

    public function testEditTaskIsSuccessful()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->logIn($user);

        $task = $this->objectManager->getRepository(Task::class)->findOneBy(['title'=>'titre1']);
        $crawler = $this->client->request('GET','/tasks/'.$task->getId().'/edit');
        $this->assertSelectorExists('form');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'titre1',
            'task[content]' => 'ceci est a faire'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302);             
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();        
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été modifiée.');

    }

    public function testToggle()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->logIn($user);

        $task = $this->objectManager->getRepository(Task::class)->findOneBy(['title'=>'titre1']);
        $isDone = $task->isDone();
        $crawler = $this->client->request('GET','/tasks/'.$task->getId().'/toggle');
        $this->assertNotEquals($task->isDone(),$isDone);
        
        
        
        
    }

    public function testdeletingNoAuthorTaskWhenAdmin()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->logIn($user);
        $task = $this->objectManager->getRepository(Task::class)->findOneBy(['id' => 1]);
        //dd($task);
        $crawler = $this->client->request('GET','/tasks/'.$task->getId().'/delete');
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert.alert-success', 'La tâche a bien été supprimée.');
    }
    
    
}