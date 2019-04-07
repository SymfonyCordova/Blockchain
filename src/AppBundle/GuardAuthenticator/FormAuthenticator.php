<?php
namespace AppBundle\GuardAuthenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

class FormAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var UserPasswordEncoder
     */
    private $encoder;

    private $container;

    /**
     * FormAuthenticator constructor.
     * @param $encoder
     * @param $container
     */
    public function __construct($encoder, $container)
    {
        $this->encoder = $encoder;
        $this->container = $container;
    }

    //3.如果 supportsToken() 返回 true，Symfony即调用 authenticateToken()。
    //此时你要做的，是检查token是否被允许登录进来，首先通过user provider来获得 User 对象，然后再检查密码和当前时间。
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $code = $this->getRequestPostAttribute('code');
        $verifyCode = $this->getSessionAttribute('captcha');
        if($code !== $verifyCode){
            throw new CustomUserMessageAuthenticationException('您输入的用验证码错误:'.$code);
        }

        try{
            $user = $userProvider->loadUserByUsername($token->getUser());
        }catch (UsernameNotFoundException $e){
            throw new CustomUserMessageAuthenticationException('您输入的用户名或密码错误');
        }

        $passwordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());

        if(!$passwordValid){
            throw new CustomUserMessageAuthenticationException('您输入的用户名或密码错误');
        }

        return new UsernamePasswordToken(
            $user,
            $user->getPassword(),
            $providerKey,
            $user->getRoles()
        );
    }

    //步骤2.检查token的类型对应符合支持这种AuthenticationProvider
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    //步骤1.将表单和request用户名和密码 创建token
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    /**
     * @return Request
     */
    protected function getRequest(){
        return $this->container->get('request');
    }

    protected function getRequestPostAttribute($name){
        return $this->getRequest()->request->get($name);
    }

    protected function getSessionAttribute($name){
        return $this->getRequest()->getSession()->get($name);
    }
}