<?php
namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;

class AlbumTest extends PHPUnit_Framework_TestCase
{

    public function testNewAlbum()
    {
        $album = new Application\Entity\Album;
        $this->assertInstanceOf('Application\Entity\Album', $album);
        unset($album);
    }

}