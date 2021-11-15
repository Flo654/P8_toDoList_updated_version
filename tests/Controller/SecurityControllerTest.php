<?php
namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\utils\LoginUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityControllerTest extends WebTestCase
{
    use LoginUser;

    private $client;
    private $objectManager;    

    public function setUp() :void
    {
        $this->client = static::createClient();
        $this->objectManager = $this->client->getContainer()->get('doctrine')->getManager();
               
    }
    
    public function testLoginPageIsUp()
    {        
        $this->client->request('GET','/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }
    
    public function testBadcredentialsLogin()
    {
        $crawler = $this->client->request('GET','/login');
        
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'badUsername',
            '_password' => 'badPassword'
        ]);
        $this->client->submit($form);             
        $this->assertResponseRedirects();        
        $this->client->followRedirect();        
        $this->assertSelectorTextContains('div.alert', 'Identifiants invalides.');
    }

    public function testRedirectionWithGoodCredentials()
    {
        $user = $this->objectManager->getRepository(User::class)->findOneBy(['username'=>'admin']);
        
        $crawler = $this->client->request('GET','/login');               
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => $user->getUsername(),
            '_password' => $user->getPassword()
        ]);
        $this->client->submit($form); 
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302)  ; 
       
    }

    public function testRedirectionLogout()
    {
        
        $this->client->request('GET','/logout');
        $isConnected = unserialize($this->client->getcontainer()->get('session')->get('_security_main'));
        $this->assertFalse($isConnected);
        $this->assertResponseRedirects($this->client->getResponse()->headers->get('Location'), 302);        
    }
}