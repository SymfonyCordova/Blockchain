<?php
namespace Biz\User;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class QqUserProvider implements UserProviderInterface{
    public function loadUserByUsername($username)
    {
        return new QqUser($username);
    }

    /**
     * http://www.symfonychina.com/doc/current/security/api_key_authentication.html
     */
    public function refreshUser(UserInterface $user)
    {
        if(!$user instanceof QqUser){
            throw new UsernameNotFoundException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return QqUser::class === $class;
    }

}