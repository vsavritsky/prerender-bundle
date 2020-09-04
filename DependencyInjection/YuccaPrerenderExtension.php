<?php

namespace Vsavritsky\PrerenderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class VsavritskyPrerenderExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('vsavritsky_prerender.backend_url', $config['backend_url']);
        $container->setParameter('vsavritsky_prerender.token', $config['token']);
        $container->setParameter('vsavritsky_prerender.force_scheme', $config['force_scheme']);
        $container->setParameter('vsavritsky_prerender.crawler_user_agents', $config['crawler_user_agents']);
        $container->setParameter('vsavritsky_prerender.ignored_extensions', $config['ignored_extensions']);
        $container->setParameter('vsavritsky_prerender.whitelist_urls', $config['whitelist_urls']);
        $container->setParameter('vsavritsky_prerender.blacklist_urls', $config['blacklist_urls']);
    }
}
