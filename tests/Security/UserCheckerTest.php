<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserCheckerTest extends TestCase
{
    public function testCheckPreAuthWithActiveUser(): void
    {
        $user = new User();
        $user->setBlocked(false);

        $checker = new UserChecker();
        $checker->checkPreAuth($user);

        $this->assertTrue(true);
    }

    public function testCheckPreAuthWithBlockedUser(): void
    {
        $this->expectException(CustomUserMessageAccountStatusException::class);

        $user = new User();
        $user->setBlocked(true);

        $checker = new UserChecker();
        $checker->checkPreAuth($user);
    }

    public function testCheckPostAuth(): void
    {
        $user = new User();
        $checker = new UserChecker();
        $checker->checkPostAuth($user);

        $this->assertTrue(true);
    }
}