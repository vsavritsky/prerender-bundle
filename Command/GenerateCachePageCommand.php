<?php

namespace Vsavritsky\PrerenderBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vsavritsky\PrerenderBundle\HttpClient\Exception;
use Vsavritsky\PrerenderBundle\Service\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GenerateCachePageCommand extends Command
{
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
            $loc = str_replace('www.', 'dev.', $loc);
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
