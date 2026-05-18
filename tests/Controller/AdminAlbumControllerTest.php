<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminAlbumControllerTest extends WebTestCase
{
    private function getIna(): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        return $em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
    }

    public function testAlbumIndexAsAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());
        $client->request('GET', '/admin/album');

        $this->assertResponseIsSuccessful();
    }

    public function testAlbumAddPageAsAdmin(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());
        $client->request('GET', '/admin/album/add');

        $this->assertResponseIsSuccessful();
    }

    public function testAlbumIndexRedirectsIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/album');

        $this->assertResponseRedirects('/login');
    }

    public function testAlbumUpdatePage(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getIna());

        $em = static::getContainer()->get('doctrine')->getManager();
        $album = $em->getRepository(\App\Entity\Album::class)->findOneBy([]);

        $client->request('GET', '/admin/album/update/' . $album->getId());

        $this->assertResponseIsSuccessful();
    }
}
