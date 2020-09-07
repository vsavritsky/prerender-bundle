<?php

namespace Vsavritsky\PrerenderBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Vsavritsky\PrerenderBundle\Entity\CachePage;
use Vsavritsky\PrerenderBundle\Event\RenderAfterEvent;
use Vsavritsky\PrerenderBundle\Event\RenderBeforeEvent;
use Vsavritsky\PrerenderBundle\Event\ShouldPrerenderEvent;
use Vsavritsky\PrerenderBundle\Events;
use Vsavritsky\PrerenderBundle\HttpClient\ClientInterface;
use Vsavritsky\PrerenderBundle\Rules\ShouldPrerender;
use Vsavritsky\PrerenderBundle\Service\CacheManager;

class KernelListener
{
    /**
     * @var string
     */
    protected $backendUrl;
    
    /**
     * @var string
     */
    protected $token;
    
    /**
     * @var null|bool
     */
    protected $forceSecureRedirect;
    
    /**
     * @var ClientInterface
     */
    protected $httpClient;
    
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    
    /** @var EntityManagerInterface */
    protected $entityManager;
    
    /** @var CacheManager  */
    protected $cacheManager;
    
    /**
     * KernelListener constructor.
     *
     * @param string $backendUrl
     * @param string $token
     * @param ClientInterface $httpClient
     * @param EventDispatcherInterface $eventDispatcher
     * @param bool $forceSecureRedirect
     * @param ShouldPrerender $rules
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        $backendUrl,
        $token,
        ClientInterface $httpClient,
        EventDispatcherInterface $eventDispatcher,
        $forceSecureRedirect,
        ShouldPrerender $rules,
        CacheManager $cacheManager
    )
    {
        $this->backendUrl = $backendUrl;
        $this->token = $token;
        $this->forceSecureRedirect = $forceSecureRedirect;
        $this->setHttpClient($httpClient);
        $this->setEventDispatcher($eventDispatcher);
        $this->rules = $rules;
        $this->cacheManager = $cacheManager;
    }
    
    /**
     * Set the HTTP client used to perform the GET request
     *
     * @param ClientInterface $httpClient
     * @return void
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    
    /**
     * Set the Event Dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @return void
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * @param RequestEvent $event
     * @return bool
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        
        if (!$event->isMasterRequest()) {
            return false;
        }
        
        //Check if we have to prerender page
        $eventshouldPrerender = new ShouldPrerenderEvent($request);
        $this->eventDispatcher->dispatch(Events::shouldPrerenderPage, $eventshouldPrerender);
        
        $shouldPrerender = $eventshouldPrerender->getShouldPrerender();
        if (is_null($shouldPrerender)) {
            //Check if we have to prerender page
            if (!$this->rules->shouldPrerenderPage($request)) {
                return false;
            }
        } elseif (false === $shouldPrerender) {
            return false;
        }
        
        $event->stopPropagation();
        
        //Dispatch event for a more custom way of retrieving response
        if ($this->forceSecureRedirect === null) {
            $scheme = $request->getScheme();
        } else {
            $scheme = $this->forceSecureRedirect ? 'https' : 'http';
        }
        
        $requestUrl = $scheme . '://' . $request->getHost() . $request->getRequestUri();
        
        $uri = rtrim($this->backendUrl, '/') . '/' . $requestUrl;
        $eventBefore = new RenderBeforeEvent($request, $uri);
        
        // @codingStandardsIgnoreStart
        $this->eventDispatcher->dispatch(Events::onBeforeRequest, $eventBefore);
        // @codingStandardsIgnoreEnd
    
        //Check if event get back a response
        if ($eventBefore->hasResponse()) {
            $response = $eventBefore->getResponse();
            if (is_string($response)) {
                $cachePage->setBody($response);
                $event->setResponse(new Response($response, 200));
            
                return true;
            } elseif ($response instanceof Response) {
                $cachePage->setBody($response->getContent());
                $event->setResponse($response);
            
                return true;
            }
        }
    
        $response = $this->cacheManager->renderUrl($requestUrl);
        
        //Dispatch event to save response
        if ($response) {
            $event->setResponse($response);
            $eventAfter = new RenderAfterEvent($request, $response);
            $this->eventDispatcher->dispatch(Events::onAfterRequest, $eventAfter);
        }
        
        return true;
    }
}
