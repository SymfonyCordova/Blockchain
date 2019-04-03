<?php
namespace AppBundle\GuardAuthenticator;

use Biz\User\Service\UserService;
use Biz\User\UserProvider;
use Codeages\Biz\Framework\Context\Biz;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class AdminAuthenticator extends AbstractGuardAuthenticator
{

    protected $failMessage = 'Invalid credentials';

    public function getCredentials(Request $request)
    {
        if($request->getPathInfo() != '/login'
            || !$request->isMethod('POST')
            || !$request->request->has('code')){
            return;
        }

        $code = $request->request->get('code');
        $verifyCode = $request->getSession()->get('captcha');
        if($code !== $verifyCode){
            return;
        }

        return array(
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {

        if(!$userProvider instanceof UserProvider){
            return ;
        }

        try{
            return $userProvider->loadUserByUsername($credentials['username']);
        }catch (\Exception $e){
            throw new CustomUserMessageAuthenticationException($this->failMessage);
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $algorithmPassword = $this->getPasswordEncoder()
                ->encodePassword($credentials['password'], $user->getSalt());
        if($user->getPassword() === $algorithmPassword){
            return true;
        }
        throw new CustomUserMessageAuthenticationException($this->failMessage);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse('/login');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse('/admin');
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse("/login");
    }

    protected function getPasswordEncoder()
    {
        return new MessageDigestPasswordEncoder('sha256');
    }
}