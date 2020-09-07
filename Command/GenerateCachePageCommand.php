<?php

namespace Vsavritsky\PrerenderBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vsavritsky\PrerenderBundle\Entity\CachePage;
use Vsavritsky\PrerenderBundle\HttpClient\Exception;
use Vsavritsky\PrerenderBundle\Service\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Console\Command\LockableTrait;

class GenerateCachePageCommand extends Command
{
    use LockableTrait;
    
    /** @var CacheManager|null */
    protected $cacheManager = null;
    
    public function __construct($name = null, CacheManager $cacheManager, ParameterBagInterface $parameterBag)
    {
        parent::__construct($name);
        $this->cacheManager = $cacheManager;
        $this->parameterBag = $parameterBag;
    }
    
    protected function configure()
    {
        $this->setName('prerender:generate:cache');
        $this->addArgument('sitemapPath', InputArgument::OPTIONAL, 'path to sitemap, default sitemap.xml', 'sitemap.xml');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
        
            return false;
        }
    
        $progressBar = new ProgressBar($output, $this->cacheManager->getCountCachePages());
        
        $page = 1;
        $cachePages = $this->cacheManager->getCachePagesByPage($page);
        while (count($cachePages)) {
            /** @var CachePage $cachePage */
            foreach ($cachePages as $cachePage) {
                $progressBar->advance();
                $response = $this->cacheManager->renderUrl($cachePage->getPath());
            }
            
            $page++;
            $cachePages = $this->cacheManager->getCachePagesByPage($page);
        }
    
        $progressBar->finish();
        
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $pathToSitemap = $projectDir.'/public/'.$input->getArgument('sitemapPath');
    
        if (!file_exists($pathToSitemap)) {
            $output->writeln(sprintf('%s', 'not found sitemap'));
            return false;
        }
        
        $xml = new \SimpleXMLElement(file_get_contents($pathToSitemap));
    
        $progressBar = new ProgressBar($output, count($xml->url));
        
        foreach ($xml->url as $url) {
            $loc = (string)$url->loc;
            $progressBar->advance();
            $response = $this->cacheManager->renderUrl($loc);
            
            if (!$response) {
                $output->writeln(sprintf('%s %s', 'error render', $loc));
            }
        }
    
        $progressBar->finish();
        
        return true;
    }
}
