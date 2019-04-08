<?php
namespace Biz\User;

use Symfony\Component\Security\Core\User\UserInterface;

class QqUser implements UserInterface,\Serializable, \ArrayAccess
{

    protected $data;

    public function setData(array $data){
        $this->data = $data;
    }

    public function getRoles()
    {
        return array($this->role);
    }

    public function setRoles(array $roles){
        $this->roles = $roles;
    }

    public function getPassword()
    {

    }

    public function getSalt()
    {

    }

    public function getUsername()
    {
        return $this->openId;
    }

    public function eraseCredentials()
    {

    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        throw new \RuntimeException("{$name} is not exist in CurrentUser.");
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->__set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->__unset($offset);
    }


}