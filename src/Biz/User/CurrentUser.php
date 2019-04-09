<?php
namespace Biz\User;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentUser implements AdvancedUserInterface, EquatableInterface, \Serializable, \ArrayAccess
{
    protected $data;

    /**
     * 检查用户的帐户是否已过期;
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * 检查用户是否被锁定; 冻结的用户是可以恢复的
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * 检查用户的密码是否已过期;
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * 检查用户是否已启用
     * @return bool
     */
    public function isEnabled()
    {
        return true;
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

    public function getRoles()
    {
        return array($this['roles']);
    }

    public function getPassword()
    {
        return $this['password'];
    }

    public function getSalt()
    {
        return $this['salt'];
    }

    public function getUsername()
    {
        return $this['username'];
    }

    public function getId()
    {
        return $this['id'];
    }

    public function getMobile(){
        return $this['mobile'];
    }

    /**
     * 仅用于清除可能存储的纯文本密码（或类似凭据 密码）
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * 检查用户是否等于当前用户
     * @param UserInterface $user
     * @return bool|void
     */
    public function isEqualTo(UserInterface $user)
    {
        if(!$user instanceof CurrentUser){
            return false;
        }

        if($this->id !== $user->getId()){
            return false;
        }

        if($this->password !== $user->getPassword()){
            return false;
        }

        if($this->salt !== $user->getSalt()){
            return false;
        }

        if($this->username !== $user->getUsername()){
            return false;
        }

        return true;
    }

    public function fromArray(array $user)
    {
        $this->data = $user;

        return $this;
    }

    public function getLoginIp()
    {
        return $this['login_ip'];
    }

    public function setLoginIp($loginIp)
    {
        $this['login_ip'] = $loginIp;

        return $this;
    }

    public function isLogin()
    {
        return empty($this->id) ? false : true;
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