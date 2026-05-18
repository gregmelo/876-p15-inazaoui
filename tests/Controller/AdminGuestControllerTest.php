<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminGuestControllerTest extends WebTestCase
{
    private function getIna(): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        return $em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
    }

    public function testGuestIndexAsAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());
        $client->request('GET', '/admin/guests');

        $this->assertResponseIsSuccessful();
    }

    public function testGuestAddPageAsAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());
        $client->request('GET', '/admin/guests/add');

        $this->assertResponseIsSuccessful();
    }

    public function testGuestIndexRedirectsIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/guests');

        $this->assertResponseRedirects('/login');
    }

    public function testBlockGuest(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());

        $em = static::getContainer()->get('doctrine')->getManager();
        $guest = $em->getRepository(User::class)->findOneBy(['email' => 'actif@example.com']);
        $wasBlocked = $guest->isBlocked();

        $client->request('GET', '/admin/guests/block/' . $guest->getId());
        $this->assertResponseRedirects('/admin/guests');

        // Restaure le statut initial
        $em->refresh($guest);
        $guest->setBlocked($wasBlocked);
        $em->flush();
    }

    public function testAddGuest(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());
        $client->request('GET', '/admin/guests/add');

        $uniqueEmail = 'nouveau_' . uniqid() . '@example.com';

        $client->submitForm('Ajouter', [
            'guest[name]' => 'Nouvel Invité',
            'guest[email]' => $uniqueEmail,
            'guest[description]' => 'Description test',
            'guest[plainPassword]' => 'password123',
        ]);

        $this->assertResponseRedirects('/admin/guests');
    }
}
