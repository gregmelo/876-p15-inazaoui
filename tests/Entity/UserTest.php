<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();
        $user->setName('Test User');
        $user->setEmail('test@example.com');
        $user->setPassword('hashedpassword');
        $user->setAdmin(false);
        $user->setBlocked(false);
        $user->setDescription('Une description');

        $this->assertEquals('Test User', $user->getName());
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('hashedpassword', $user->getPassword());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isBlocked());
        $this->assertEquals('Une description', $user->getDescription());
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertEquals('test@example.com', $user->getUserIdentifier());
    }

    public function testGetRolesForAdmin(): void
    {
        $user = new User();
        $user->setAdmin(true);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testGetRolesForGuest(): void
    {
        $user = new User();
        $user->setAdmin(false);

        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->setPassword('hashedpassword');
        $user->eraseCredentials();

        $this->assertEquals('hashedpassword', $user->getPassword());
    }

    public function testIsBlocked(): void
    {
        $user = new User();
        $user->setBlocked(true);

        $this->assertTrue($user->isBlocked());
    }
}