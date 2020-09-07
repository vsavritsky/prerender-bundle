<?php

namespace Vsavritsky\PrerenderBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vsavritsky\PrerenderBundle\Service\CacheManager;
use Symfony\Component\Console\Command\LockableTrait;

class ClearCachePageCommand extends Command
{
    use LockableTrait;
    
    /** @var CacheManager|null */
    protected $cacheManager = null;
    
    public function __construct($name = null, CacheManager $cacheManager)
    {
        parent::__construct($name);
        $this->cacheManager = $cacheManager;
    }
    
    protected function configure()
    {
        $this->setName('prerender:cache:clear');
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
        
        $this->cacheManager->clear();
    }
}
