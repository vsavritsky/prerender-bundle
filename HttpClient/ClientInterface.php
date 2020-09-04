<?php

namespace Vsavritsky\PrerenderBundle\HttpClient;

interface ClientInterface
{
    public function send($url);
}
