<?php
namespace AppBundle\GuardAuthenticator;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class MobileCodeGuardAuthenticator extends AbstractGuardAuthenticator{

    protected $container;

    protected $mobileSecret;

    /**
     * MobileCodeGuardAuthenticator constructor.
     * @param $container
     */
    public function __construct($container, $mobileSecret)
    {
        $this->container = $container;
        $this->mobileSecret = $mobileSecret;
    }


    public function getCredentials(Request $request)
    {
        if($request->getPathInfo() !== $this->generateUrl('phone_code_login')){
            return;
        }

        if($request->getMethod() !== "POST"){
            return;
        }

        if(!$this->getRequestPostHasAttribute('mobile') || !$this->getRequestPostHasAttribute('code')){
            return;
        }

        return array(
            'mobile'     => $this->getRequestPostAttribute('mobile'),
            'code'    => $this->getRequestPostAttribute('code'),
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $mobile = $credentials['mobile'];

        return $userProvider->loadUserByUsername($mobile);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if($user->getMobile() !== $credentials['mobile']){
            return false;
        }

        $redisMobileCode = $this->getRedis()->get($this->mobileSecret.$credentials['mobile']);

        if(!$redisMobileCode){
            return false;
        }

        return $credentials['code'] === $redisMobileCode;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->generateUrl('login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->generateUrl('admin_homepage'));
    }

    public function supportsRememberMe()
    {
        return true;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->generateUrl('login'));
    }

    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH){
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * @return Request
     */
    public function getRequest(){
        return $this->container->get('request');
    }

    public function getRequestPostAttribute($name){
        return $this->getRequest()->request->get($name);
    }

    public function getRequestPostHasAttribute($name){
        return $this->getRequest()->request->has($name);
    }

    public function getBiz(){
        return $this->container->get('biz');
    }

    /**
     * @return \Redis
     */
    public function getRedis(){
        $biz = $this->getBiz();

        return $biz['redis'];
    }
}