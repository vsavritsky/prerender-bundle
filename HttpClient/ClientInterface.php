<?php

namespace Vsavritsky\PrerenderBundle\HttpClient;

use Symfony\Component\HttpFoundation\Response;

interface ClientInterface
{
    public function send($url) : Response;
}
