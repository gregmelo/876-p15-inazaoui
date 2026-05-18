<?php

namespace App\Tests\Entity;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $media = new Media();
        $user = new User();
        $album = new Album();

        $media->setTitle('Photo test');
        $media->setPath('uploads/test.jpg');
        $media->setUser($user);
        $media->setAlbum($album);

        $this->assertEquals('Photo test', $media->getTitle());
        $this->assertEquals('uploads/test.jpg', $media->getPath());
        $this->assertSame($user, $media->getUser());
        $this->assertSame($album, $media->getAlbum());
    }

    public function testNullableAlbum(): void
    {
        $media = new Media();
        $media->setAlbum(null);

        $this->assertNull($media->getAlbum());
    }
}