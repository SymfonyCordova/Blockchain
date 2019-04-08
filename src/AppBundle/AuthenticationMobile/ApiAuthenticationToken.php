<?php
namespace AppBundle\AuthenticationMobile;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * 参考UsernamePasswordToken
 *
 * 在Symfony的security context中，token中的role非常重要。
 * 一个token，呈现了包含在请求中的用户认证数据。
 * 一旦请求被认证，token会保留用户的数据，并把这些数据在security context中进行传递。
 * 首先，创建你的token类。它可以把相关的全部信息传入你的authentication provider。
 *
 */
class ApiAuthenticationToken extends AbstractToken
{
    public $created;
    public $digest;
    public $nonce;

    /**
     * SmsCodeAuthenticationToken constructor.
     * @param $created
     */
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // 如果用户持有roles，视其为已认证
        $this->setAuthenticated(count($roles) >0 );
    }

    public function getCredentials()
    {
        return "";
    }
}