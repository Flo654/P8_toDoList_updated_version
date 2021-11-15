<?php
namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\utils\LoginUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use LoginUser;

    private $client;
    private $objectManager;

    public function setUp():void
    {
        $this->client = static::createClient();
        $this->objectManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testUserListIsUp()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->logIn($user);
        $this->client->request('GET','/users');
        //dd($this->client->getResponse()->getContent());
        $this->assertSelectorTextContains(
            'h1',
            'Liste des utilisateurs'
        );
        $this->assertSelectorExists('table');
    }

    public function testCreateUserPageIsUp()
    {

        $this->client->request('GET','/users/create');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorTextContains('h1','Créer un utilisateur');        
    }

    public function testCreateUserRedirectionIfConnectedAsUser()
    {
        $user= $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'user']);
        $this->logIn($user);
        $isConnected = ($this->client->getcontainer()->get('session')->get('_security_main'))?true:false; 
        
        $this->client->request('GET','/users/create');
        
        $this->assertTrue($isConnected);
        $this->assertFalse(in_array("ROLE_ADMIN", $user->getRoles()));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());        
    }
    
    public function testCreatingANewUser()
    {
        $crawler = $this->client->request('GET','/users/create');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'Username3',
            'user[password][first]' => 'pa$$word',
            'user[password][second]' => 'pa$$word',
            'user[email]' => 'username3@email.com',
            'user[isAdmin]' => true
        ]);
        $this->client->submit($form);      
        
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302);             
        $this->client->followRedirect(); 
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();        
        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur a bien été ajouté.');        
    }

    public function testEditUserRedirectionIfNotAdmin()
    {
        $user= $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'user']);
        $this->logIn($user);
        $isConnected = ($this->client->getcontainer()->get('session')->get('_security_main'))?true:false; 
        
        $this->client->request('GET','/users/'.$user->getId().'/edit');
        
        $this->assertTrue($isConnected);
        $this->assertFalse(in_array("ROLE_ADMIN", $user->getRoles()));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testEditAnUser()
    {
        $user= $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        $this->logIn($user);
        $isConnected = ($this->client->getcontainer()->get('session')->get('_security_main'))?true:false; 
        
        $crawler = $this->client->request('GET','/users/'.$user->getId().'/edit');
        $this->assertTrue($isConnected);
        $this->assertTrue(in_array("ROLE_ADMIN", $user->getRoles()));
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'admin',
            'user[password][first]' => 'admin1',
            'user[password][second]' => 'admin1',
            'user[email]' => 'admin@admin.com',
            'user[isAdmin]' => true
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302);             
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();        
        $this->assertSelectorTextContains('div.alert.alert-success', 'L\'utilisateur a bien été modifié');
    }
}