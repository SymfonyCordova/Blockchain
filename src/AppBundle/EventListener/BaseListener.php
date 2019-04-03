<?php
namespace AppBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * BaseListener constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    protected function inUrlWhiteList($pathInfo)
    {
        $urlWhiteList = array(
            '/api',
        );

        foreach ($urlWhiteList as $url) {
            if (stripos($pathInfo, $url) === 0) {
                return true;
            }
        }

        return false;
    }

    protected function createService($alias)
    {
        return $this->container->get('biz')->service($alias);
    }
}