<?php

namespace Vsavritsky\PrerenderBundle\Tests;


use PHPUnit\Framework\TestCase;
use Vsavritsky\PrerenderBundle\YuccaPrerenderBundle;

class VsavritskyPrerenderBundleTest extends TestCase
{
    public function testGetName()
    {
        $bundle = new VsavritskyPrerenderBundle();
        $this->assertEquals('YuccaPrerenderBundle', $bundle->getName());
    }
}
