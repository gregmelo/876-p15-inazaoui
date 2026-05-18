<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('body');
    }

    public function testGuestsPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/guests');

        $this->assertResponseIsSuccessful();
    }

    public function testGuestPageWithActiveGuest(): void
    {
        $client = static::createClient();

        $em = static::getContainer()->get('doctrine')->getManager();
        $guest = $em->getRepository(User::class)->findOneBy([
            'email' => 'actif@example.com'
        ]);

        $client->request('GET', '/guest/' . $guest->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testGuestPageWithBlockedGuestRedirects(): void
    {
        $client = static::createClient();

        $em = static::getContainer()->get('doctrine')->getManager();
        $guest = $em->getRepository(User::class)->findOneBy([
            'email' => 'bloque@example.com'
        ]);

        $client->request('GET', '/guest/' . $guest->getId());

        $this->assertResponseRedirects('/guests');
    }

    public function testPortfolioPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/portfolio');

        $this->assertResponseIsSuccessful();
    }

    public function testAboutPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/about');

        $this->assertResponseIsSuccessful();
    }

    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            '_username' => 'ina@zaoui.com',
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects();
    }

    public function testAdminRedirectsIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/media');

        $this->assertResponseRedirects('/login');
    }
}