<?php
namespace Biz\User;

use Biz\User\Service\UserconnectionService;
use Codeages\Biz\Framework\Context\Biz;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class QqUserProvider implements UserProviderInterface{

    private $container;

    /**
     * QqUserProvider constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }


    public function loadUserByUsername($params)
    {
        $user = $this->getUserConnService()->getByOpenId($params['openid']);
        $user['register'] = true;
        if(empty($user)){
            $user = new QqUser();
            $user->setData($params);
            $user->setRoles(array('ROLE_THIRD_USER'));
            $user['register'] = !$user['register'];
        }

        return $user;
    }

    /**
     * http://www.symfonychina.com/doc/current/security/api_key_authentication.html
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return QqUser::class === $class;
    }

    /**
     * @return UserconnectionService
     */
    private function getUserConnService(){
        return $this->getBiz()->service("User:UserconnectionService");
    }


    /**
     * @return Biz
     */
    private function getBiz(){
        return $this->container->get('biz');
    }
}