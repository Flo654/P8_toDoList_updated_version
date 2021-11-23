<?php
namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase 
{
    public function getNewUser(): User
    {
        $user = new User;
        $user->setUsername('nouvel user');
        $user->setPassword('123456');
        $user->setEmail('user1@user1.com');
        return $user;
    }
    private function assertHasErrors(User $user, int $number)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);            
        $this->assertCount($number, $errors);
    }

    public function testValidUserEntity()
    {
        $this->assertHasErrors($this->getNewUser(), 0);        
    }

    public function testNotOkIfUsernameIsBlank()
    {
        $blankUsername = $this->getNewUser();
        $blankUsername->setUsername('');
        $this->assertHasErrors($blankUsername, 1);        
    }

    public function testNotOkIfEmailAlreadyExists()
    {
        $sameEmail = $this->getNewUser();        
        $sameEmail->setEmail('user@user.com');
        $this->assertHasErrors($sameEmail, 1);        
    }

    public function testEmailFormatNotOk()
    {
        $wrongEMail = $this->getNewUser();        
        $wrongEMail->setEmail('userauser.com');
        $this->assertHasErrors($wrongEMail, 1);        
    }

    public function testNotOkIfEmailIsBlank()
    {
        $blankEMail = $this->getNewUser();        
        $blankEMail->setEmail('');
        $this->assertHasErrors($blankEMail, 1);        
    }


}