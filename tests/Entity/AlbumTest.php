<?php

namespace App\Tests\Entity;

use App\Entity\Album;
use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $album = new Album();
        $album->setName('Album Test');

        $this->assertEquals('Album Test', $album->getName());
        $this->assertNull($album->getId());
    }
}