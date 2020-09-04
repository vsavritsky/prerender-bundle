<?php

namespace Vsavritsky\PrerenderBundle\Tests\HttpClient;

use PHPUnit\Framework\TestCase;
use Vsavritsky\PrerenderBundle\HttpClient\Curl;
use Vsavritsky\PrerenderBundle\HttpClient\Exception;

class CurlTest extends TestCase
{
    public function testSend()
    {
        $curlClient = new Curl();
        $this->assertInstanceOf('Vsavritsky\PrerenderBundle\HttpClient\ClientInterface', $curlClient);

        $resp = $curlClient->send('http://www.example.com');
        $this->assertContains('<title>Example Domain</title>', $resp);
    }
}
