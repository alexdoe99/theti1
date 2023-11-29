<?php
// tests/Entity/UserTest.php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Winners;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;


class UserTest extends TestCase
{
    public function testEmail()
    {
        $user = new User();
        $email = 'user@example.com';
        $user->setEmail($email);
        
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($email, $user->getUserIdentifier());
    }

    public function testRoles()
    {
        $user = new User();
        $roles = ['ROLE_ADMIN'];

        $user->setRoles($roles);
        
        // Assert both roles are returned, including the default one
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
    }

    public function testPassword()
    {
        $user = new User();
        $password = 'securepassword';
        $user->setPassword($password);
        
        $this->assertEquals($password, $user->getPassword());
    }

    public function testWinningTickets()
    {
        $user = new User();
        $winner = new Winners();
        
        $this->assertInstanceOf(Collection::class, $user->getWinningTickets());
        
        $user->addWinningTicket($winner);
        $this->assertCount(1, $user->getWinningTickets());

        $user->removeWinningTicket($winner);
        $this->assertCount(0, $user->getWinningTickets());
    }

    public function testPersonalInformation()
    {
        $user = new User();
        $user->setAddress('123 Main Street')
             ->setCity('Anytown')
             ->setCountry('Countryland')
             ->setAboutUs('About user text')
             ->setName('John')
             ->setSurname('Doe')
             ->setNewsletter(true);

        $this->assertEquals('123 Main Street', $user->getAddress());
        $this->assertEquals('Anytown', $user->getCity());
        $this->assertEquals('Countryland', $user->getCountry());
        $this->assertEquals('About user text', $user->getAboutUs());
        $this->assertEquals('John', $user->getName());
        $this->assertEquals('Doe', $user->getSurname());
        $this->assertTrue($user->isNewsletter());
    }

    // You might want to add tests for nullable fields to ensure they handle null values correctly.
}
