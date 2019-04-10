<?php

namespace AppBundle\Handler;

use Biz\User\Service\UserService;
use Codeages\Biz\Framework\Context\Biz;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Custom login listener.
 */
class LoginSuccessHandler
{
    /**
     * @var AuthorizationChecker
     */
    private $checker;

    /**
     * @var
     */
    private $container;

    /**
     * Constructor.
     *
     * @param AuthorizationChecker $checker
     * @param Doctrine             $doctrine
     */
    public function __construct(ContainerInterface $container, AuthorizationChecker $checker)
    {
        $this->container = $container;
        $this->checker = $checker;
    }

    /**
     * Do the magic.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($this->checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // user has just logged in
        }

        if ($this->checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // user has logged in using remember_me cookie
        }

        // do some other magic here
        $user = $event->getAuthenticationToken()->getUser();
        //$user->setPermissions(PermissionBuilder::instance()->getPermissionsByRoles($user->getRoles()));

        $request = $event->getRequest();
        $sessionId = $request->getSession()->getId();
        //$request->getSession()->set('loginIp', $request->getClientIp());

        //$this->getUserService()->markLoginInfo();
        $prefix = $this->container->getParameter('redis.prefix');
        $this->getUserService()->rememberLoginSessionId($user['id'], $prefix.$sessionId);
        //$this->getUserService()->markLoginSuccess($user['id'], $request->getClientIp());
    }

    /**
     * @return UserService
     */
    private function getUserService()
    {
        return $this->getBiz()->service("User:UserService");
    }

    /**
     * @return Biz
     */
    private function getBiz(){
        return $this->container->get('biz');
    }
}
