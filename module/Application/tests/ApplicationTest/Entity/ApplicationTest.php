<?php
namespace ApplicationTest\Entity;

use PHPUnit_Framework_TestCase;

class ApplicationTest extends PHPUnit_Framework_TestCase
{

    public function testNewAlbum()
    {
        $album = new Album;
        $this->assertInstanceOf('Application\Entity\Album', $album);
        unset($album);
    }

}