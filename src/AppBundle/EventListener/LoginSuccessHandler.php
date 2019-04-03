<?php
namespace AppBundle\EventListener;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginSuccessHandler
{
    protected $container;
    protected $checker;

    /**
     * LoginSuccessHandler constructor.
     */
    public function __construct(ContainerInterface $container, AuthorizationChecker $checker)
    {
        $this->container = $container;
        $this->checker = $checker;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->getLogger()->info(sprintf("login success authenticationToken:%s", $event->getAuthenticationToken()));
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->container->get("logger");
    }
}