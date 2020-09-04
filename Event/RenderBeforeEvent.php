<?php

namespace Vsavritsky\PrerenderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RenderBeforeEvent
 * @package Yucca\PrerenderBundle\Event
 */
class RenderBeforeEvent extends Event
{
    protected $request;
    protected $response;
    protected $prerenderUrl;

    /**
     * RenderBeforeEvent constructor.
     *
     * @param Request $request
     * @param string  $prerenderUrl
     */
    public function __construct(Request $request, $prerenderUrl)
    {
        $this->request = $request;
        $this->prerenderUrl = $prerenderUrl;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getPrerenderUrl()
    {
        return $this->prerenderUrl;
    }

    /**
     * @param string $prerenderUrl
     *
     * @return $this
     */
    public function setPrerenderUrl($prerenderUrl)
    {
        $this->prerenderUrl = $prerenderUrl;

        return $this;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return isset($this->response);
    }
}
