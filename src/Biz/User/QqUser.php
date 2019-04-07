<?php
namespace Biz\User;

use Symfony\Component\Security\Core\User\UserInterface;

class QqUser implements UserInterface{

    private $openId;

    public function getRoles()
    {
        return array('ROLE_THIRD_USER');
    }

    public function getPassword()
    {

    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->openId;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

}