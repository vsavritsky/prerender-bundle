<?php

namespace Vsavritsky\PrerenderBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vsavritsky\PrerenderBundle\DependencyInjection\Configuration;
use Vsavritsky\PrerenderBundle\DependencyInjection\PrerenderExtension;

class PrerenderExtensionTest extends TestCase
{
    protected $containerBuilder;

    /**
     * @param $class
     * @param $propertyName
     * @return mixed
     */
    public static function getReflectedPropertyValue($class, $propertyName)
    {
        $reflectedClass = new \ReflectionClass($class);
        $property = $reflectedClass->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($class);
    }

    /**
     * @param mixed  $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals(
            $value,
            $this->containerBuilder->getParameter($key),
            sprintf('%s parameter is correct', $key)
        );
    }

    /**
     * @return mixed
     */
    public function testDefaultLoad()
    {
        $config = new Configuration();
        $defaultIgnoredExtensions = self::getReflectedPropertyValue($config, 'defaultIgnoredExtensions');
        $defaultCrawlerUserAgents = self::getReflectedPropertyValue($config, 'defaultCrawlerUserAgents');

        $this->containerBuilder = new ContainerBuilder();
        $extension = new VsavritskyPrerenderExtension();
        $extension->load(array(), $this->containerBuilder);

        $this->assertParameter('http://service.prerender.io', 'vsavritsky_prerender.backend_url');
        $this->assertParameter($defaultCrawlerUserAgents, 'vsavritsky_prerender.crawler_user_agents');
        $this->assertParameter($defaultIgnoredExtensions, 'vsavritsky_prerender.ignored_extensions');
        $this->assertParameter(array(), 'vsavritsky_prerender.whitelist_urls');
        $this->assertParameter(array(), 'vsavritsky_prerender.blacklist_urls');
    }

    /**
     * @return mixed
     */
    public function testBackendUrl()
    {
        $this->containerBuilder = new ContainerBuilder();
        $extension = new VsavritskyPrerenderExtension();
        $extension->load(array(array('backend_url'=>'http://localhost:3000')), $this->containerBuilder);

        $this->assertParameter('http://localhost:3000', 'vsavritsky_prerender.backend_url');
    }

    /**
     * @return mixed
     */
    public function testCrawler()
    {
        $this->containerBuilder = new ContainerBuilder();
        $extension = new VsavritskyPrerenderExtension();
        $extension->load(array(array('crawler_user_agents'=>array('My new bot'))), $this->containerBuilder);

        $this->assertParameter(array('My new bot'), 'vsavritsky_prerender.crawler_user_agents');
    }

    /**
     * @return mixed
     */
    public function testIgnoredExtensions()
    {
        $this->containerBuilder = new ContainerBuilder();
        $extension = new VsavritskyPrerenderExtension();
        $extension->load(array(array('ignored_extensions'=>array('.io'))), $this->containerBuilder);

        $this->assertParameter(array('.io'), 'vsavritsky_prerender.ignored_extensions');
    }

    /**
     * @return mixed
     */
    public function testWhitelist()
    {
        $this->containerBuilder = new ContainerBuilder();
        $extension = new VsavritskyPrerenderExtension();
        $extension->load(array(array('whitelist_urls'=>array('/users'))), $this->containerBuilder);

        $this->assertParameter(array('/users'), 'vsavritsky_prerender.whitelist_urls');
    }

    /**
     * @return mixed
     */
    public function testBlacklist()
    {
        $this->containerBuilder = new ContainerBuilder();
        $extension = new VsavritskyPrerenderExtension();
        $extension->load(array(array('blacklist_urls'=>array('/users'))), $this->containerBuilder);

        $this->assertParameter(array('/users'), 'vsavritsky_prerender.blacklist_urls');
    }
}
