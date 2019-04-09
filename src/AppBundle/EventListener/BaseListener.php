<?php
namespace AppBundle\EventListener;

use Codeages\Biz\Framework\Context\Biz;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /**
     * @return Biz
     */
    protected function getBiz(){
        return $this->container->get('biz');
    }

    protected function createService($alias)
    {
        return $this->getBiz()->service($alias);
    }

    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH){
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}