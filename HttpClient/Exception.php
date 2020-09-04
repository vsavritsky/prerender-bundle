<?php

namespace Vsavritsky\PrerenderBundle\HttpClient;

use Throwable;

class Exception extends \Exception
{
    public $header;
    public $body;

    /**
     * Exception constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param string         $header
     * @param string         $body
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null, $header, $body)
    {
        parent::__construct($message, $code, $previous);
        $this->header = $header;
        $this->body = $body;
    }
}
