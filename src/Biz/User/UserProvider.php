<?php
namespace Biz\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Biz\User\CurrentUser;

class UserProvider implements UserProviderInterface
{
    private $container;

    /**
     * UserProvider constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }


    public function loadUserByUsername($username)
    {
        //获取用户信息
        $user = $this->getUserService()->getUserByLoginField($username);
        $currentUser = new CurrentUser();
        $currentUser->fromArray($user);
        $currentUser->setLoginIp($this->container->get('request')->getClientIp());
        //todo 获取用户的权限
        $biz = $this->getBiz();
        $biz['user'] = $currentUser;
        return $currentUser;
    }

    public function refreshUser(UserInterface $user)
    {
        if(!$user instanceof CurrentUser){
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class == 'Biz\User\CurrentUser';
    }

    /**
     * @return \Biz\User\Service\UserService
     */
    protected function getUserService()
    {
        return $this->getBiz()->service("User:UserService");
    }

    /**
     * @return \Codeages\Biz\Framework\Context\Biz
     */
    protected function getBiz()
    {
        return $this->container->get('biz');
    }

}