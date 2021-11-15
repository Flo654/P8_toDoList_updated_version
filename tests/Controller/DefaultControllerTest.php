<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\utils\LoginUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use LoginUser;
    private $objectManager;
    private $client;    
   
    public function setUp(): void
    {       
        $this->client = static::createClient();
        $this->objectManager = $this->client->getContainer()->get('doctrine')->getManager();
    } 

    public function testIndexRedirectionOk()
    {          
        $this->client->request('GET', '/');        
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302)  ;  
    }

    public function testIndexWhenUserIsConnectedH1()
    {
        $user= $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->logIn($user);
        $this->client->request('GET','/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'h1',
            'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !'
        );
    }
}
