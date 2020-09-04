<?php

namespace Vsavritsky\PrerenderBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ShouldPrerenderEvent extends Event
{
    protected $request;
    protected $shouldPrerender;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param bool $shouldPrerender
     *
     * @return $this
     */
    public function setShouldPrerender(bool $shouldPrerender)
    {
        $this->shouldPrerender = $shouldPrerender;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShouldPrerender() : ?bool
    {
        return $this->shouldPrerender;
    }
}
