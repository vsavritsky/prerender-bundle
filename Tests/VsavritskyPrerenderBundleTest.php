<?php

namespace Vsavritsky\PrerenderBundle\Tests;

use PHPUnit\Framework\TestCase;
use Vsavritsky\PrerenderBundle\PrerenderBundle;

class VsavritskyPrerenderBundleTest extends TestCase
{
    public function testGetName()
    {
        $bundle = new PrerenderBundle();
        $this->assertEquals('YuccaPrerenderBundle', $bundle->getName());
    }
}
