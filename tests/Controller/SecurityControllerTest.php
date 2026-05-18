<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            '_username' => 'wrong@example.com',
            '_password' => 'wrongpassword',
        ]);

        $this->assertResponseRedirects('/login');
    }

    public function testLogout(): void
    {
        $client = static::createClient();

        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => 'ina@zaoui.com']);

        $client->loginUser($user);
        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
    }
}