<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminMediaControllerTest extends WebTestCase
{
    private function getIna(): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        return $em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
    }

    private function getGuest(): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        return $em->getRepository(User::class)->findOneBy(['email' => 'actif@example.com']);
    }

    public function testMediaIndexAsAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());
        $client->request('GET', '/admin/media');

        $this->assertResponseIsSuccessful();
    }

    public function testMediaIndexAsGuest(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getGuest());
        $client->request('GET', '/admin/media');

        $this->assertResponseIsSuccessful();
    }

    public function testMediaIndexRedirectsIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/media');

        $this->assertResponseRedirects('/login');
    }

    public function testMediaAddPageAsAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());
        $client->request('GET', '/admin/media/add');

        $this->assertResponseIsSuccessful();
    }

    public function testMediaAddPageAsGuest(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getGuest());
        $client->request('GET', '/admin/media/add');

        $this->assertResponseIsSuccessful();
    }
}