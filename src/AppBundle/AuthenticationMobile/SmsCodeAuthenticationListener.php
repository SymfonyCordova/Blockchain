<?php
namespace AppBundle\AuthenticationMobile;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;

/**
 * 接下来，你需要一个listener对firewall进行监听。
 * 监听，负责守备着"指向防火墙的请求"，并调用authentication provider。
 * 一个listener，必须是 ListenerInterface 的一个实例。
 * 一个security listener ，应能处理 GetResponseEvent 事件，
 *
 * 并在认证成功时，于token storage中设置一个authenticated token（已认证token）。
 */
class SmsCodeAuthenticationListener implements ListenerInterface{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var AuthenticationProviderManager
     */
    protected $authenticationManager;

    /**
     * SmsCodeAuthenticationListener constructor.
     * @param $tokenStorage
     * @param $authenticationManager
     */
    public function __construct($tokenStorage, $authenticationManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }


    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([a-zA-Z0-9+\/]+={0,2})", Created="([^"]+)"/';
        if (!$request->headers->has('x-wsse') || 1 !== preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
            return;
        }

        $token = new SmsCodeAuthenticationToken();
        $token->setUser($matches[1]);

        $token->digest   = $matches[2];
        $token->nonce    = $matches[3];
        $token->created  = $matches[4];

        try {
            //这里 authenticationManager 将提供的SmsAuthenticationProvider进行SmsCodeAuthenticationToken进行验证
            //1.先使用SmsAuthenticationProvider supports方法验证当前传入的token的类型
            //2.再使用SmsAuthenticationProvider的authenticate方法验证
                //authenticate 会使用UserProvider的loadUserByUsername()加载用户
                //比如验证密码 等等这需要根据具体业务发挥
            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);

            return;
        } catch (AuthenticationException $failed) {
            //可于此处做些日志
            // 要拒绝认证应清除token。这会重定向到登录页。
            // 确保只清除你的token，而不是其他authentication listeners的token。
            // $token = $this->tokenStorage->getToken();
            // if ($token instanceof WsseUserToken && $this->providerKey === $token->getProviderKey()) {
            //     $this->tokenStorage->setToken(null);
            // }
            // return;
        }

        //默认时，拒绝授权
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $event->setResponse($response);
    }

}