<?php

namespace Vsavritsky\PrerenderBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Vsavritsky\PrerenderBundle\Entity\CachePage;
use Vsavritsky\PrerenderBundle\HttpClient\ClientInterface;

class CacheManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;
    
    /** @var \Doctrine\Persistence\ObjectRepository */
    protected $cachePageRepository;
    
    /** @var string */
    protected $backendUrl;
    
    /** @var string */
    protected $token;
    
    /** @var string */
    protected $cachePeriod;
    
    protected $limit = 10;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        ClientInterface $httpClient,
        $backendUrl,
        $token,
        $cachePeriod
    )
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->cachePageRepository = $this->entityManager->getRepository(CachePage::class);
        
        $this->backendUrl = $backendUrl;
        $this->token = $token;
        $this->cachePeriod = $cachePeriod;
    }
    
    public function renderUrl($requestUrl, $force = false)
    {
        $requestUrl = $this->normalize($requestUrl);
        $needUpdate = false;
        
        $prerenderUrl = rtrim($this->backendUrl, '/') . '/' . $requestUrl;
        
        /** @var CachePage $cachePage */
        $cachePage = $this->cachePageRepository->findOneBy(['path' => $requestUrl]);
        
        if (!$cachePage) {
            $needUpdate = true;
            $cachePage = new CachePage();
            $cachePage->setPath($requestUrl);
        } else {
            $updateAt = $cachePage->getUpdatedAt();
            
            $dateTime = new \DateTime();
            $dateTime->sub(new \DateInterval($this->cachePeriod));
            
            if ($updateAt <= $dateTime) {
                $needUpdate = true;
            }
            
            if (!$cachePage->getBody()) {
                $needUpdate = true;
            }
            
            if ($force) {
                $needUpdate = true;
            }
        }
        
        if ($needUpdate) {
            try {
                $response = $this->httpClient->send($prerenderUrl, $this->token);
                $cachePage->setHttpCode($response->getStatusCode());
                $cachePage->setBody($response->getContent());
            } catch (\Vsavritsky\PrerenderBundle\HttpClient\Exception $e) {
                var_dump($e->getMessage());
            }
            
            $this->entityManager->persist($cachePage);
            $this->entityManager->flush();
        }
        
        return new Response($cachePage->getBody(), 200);
    }
    
    public function clear()
    {
        $page = 1;
        $cachePages = $this->getCachePagesByPage($page);
        
        while (count($cachePages)) {
            foreach ($cachePages as $cachePage) {
                $this->entityManager->remove($cachePage);
            }
            
            $page++;
            $cachePages = $this->getCachePagesByPage($page);
        }
        
        $this->entityManager->flush();
    }
    
    public function getCachePagesByPage($page = 1)
    {
        return $this->cachePageRepository->findBy([], ['id' => 'asc'], $this->limit, ($this->limit * ($page - 1)));
    }
    
    
    public function getCountCachePages()
    {
        return $this->cachePageRepository->count([]);
    }
    
    protected function normalize($url)
    {
        $url = $this->removeParams($url,
            [
                'pm_block',
                'pm_position',
                'pm_source',
                'cm_id',
                'yclid',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_content',
                'utm_term'
            ]
        );
        
        return $url;
    }
    
    protected function removeParams($url, $params = [])
    {
        foreach ($params as $param) {
            $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
            $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
        }
        return $url;
    }
}