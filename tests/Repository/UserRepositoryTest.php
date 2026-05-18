<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    public function testFindActiveGuests(): void
    {
        self::bootKernel();
        $repo = static::getContainer()->get(UserRepository::class);

        $guests = $repo->findActiveGuests();

        $this->assertIsArray($guests);
        foreach ($guests as $guest) {
            $this->assertFalse($guest->isAdmin());
            $this->assertFalse($guest->isBlocked());
        }
    }

    public function testFindAllGuests(): void
    {
        self::bootKernel();
        $repo = static::getContainer()->get(UserRepository::class);

        $guests = $repo->findAllGuests();

        $this->assertIsArray($guests);
        $this->assertGreaterThanOrEqual(2, count($guests));
        foreach ($guests as $guest) {
            $this->assertFalse($guest->isAdmin());
        }
    }

    public function testUpgradePassword(): void
    {
        self::bootKernel();
        $repo = static::getContainer()->get(UserRepository::class);
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $oldPassword = $user->getPassword();

        $repo->upgradePassword($user, 'newhashedpassword');

        $this->assertEquals('newhashedpassword', $user->getPassword());

        // Restaure l'ancien mot de passe
        $repo->upgradePassword($user, $oldPassword);
    }
}
