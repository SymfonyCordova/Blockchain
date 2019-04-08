<?php
namespace AppBundle\GuardAuthenticator;

use AppBundle\Common\CurlToolkit;
use Biz\User\Service\UserconnectionService;
use Codeages\Biz\Framework\Context\Biz;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class QqGuardAuthenticator extends AbstractGuardAuthenticator{

    const OAUTH_AUTHORIZE = 'https://graph.qq.com/oauth2.0/authorize';

    const OAUTH_TOKEN_URL = 'https://graph.qq.com/oauth2.0/token';

    const OAUTH_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    const OAUTH_USER_INFO_URL = "https://graph.qq.com/user/get_user_info";

    const PROVIDER_ID = 'QQ';

    private $isRegister = false;

    private $container;

    private $appId;

    private $appSecret;

    /**
     * QqGuardAuthenticator constructor.
     * @param $container
     */
    public function __construct($container, $appId, $appSecret)
    {
        $this->container = $container;
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
    }


    public function getCredentials(Request $request)
    {
        if($request->getPathInfo() !== $this->generateUrl('auth_qq')){
            return ;
        }

        if ($this->getRequestGetHasAttribute('code')) {
            return array('code' => $this->getRequestGetAttribute('code'));
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $accessTokenParams = $this->getAccessToken($credentials);
        $openParams  = $this->getOpenId($accessTokenParams["access_token"]);
        $userInfo = $this->getQqUserInfo($accessTokenParams["access_token"], $openParams['openid']);
        $params = array_merge($accessTokenParams, $openParams, $userInfo);
        $params['provider_id'] = self::PROVIDER_ID;
        $this->getRequest()->attributes->add($params);
        $user = $userProvider->loadUserByUsername($params);
        $this->isRegister = $user['register'];
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->generateUrl('login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if($this->isRegister){
            return new RedirectResponse($this->generateUrl('qq_register'), '302', $request->attributes->all());
        }
        return new RedirectResponse($this->generateUrl('homepage'));
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $queries = array(
            'response_type' => 'code',
            'client_id' => $this->appId,
            'redirect_uri' => urlencode($request->getUri()),
            'state' =>  md5(uniqid(rand(), TRUE)), //todo 改成系统生成的
        );
        $redirectUrl = sprintf(self::OAUTH_AUTHORIZE.'?%s', http_build_query($queries));

        return new RedirectResponse($redirectUrl);
    }

    private function getAccessToken(array $credentials)
    {
        try {
            $url = sprintf(
                self::OAUTH_TOKEN_URL.'?client_id=%s&client_secret=%s&code=%s&grant_type=authorization_code&redirect_uri=%s',
                $this->appId,
                $this->appSecret,
                $credentials['code'],
                urlencode($this->getRequest()->getUri())
            );

            $response = CurlToolkit::request("GET", $url);
            if(strpos($response, "callback") !== false){
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
                $msg = json_decode($response,true);

                if(isset($msg['error'])){
                    throw new AuthenticationException(
                        sprintf('GET %s AccessToken failure errcode:%s errmsg:%s',
                            self::PROVIDER_ID , $msg['error'], $msg['error_description'])
                    );
                }
            }

            $params = array();
            parse_str($response, $params);
            return $params;
        } catch (\Exception $ex) {
            throw new AuthenticationException(
                sprintf('GET %s AccessToken OAuth server is down error:%s',self::PROVIDER_ID , $ex)
            );
        }


    }

    private function getOpenId($accessToken){
        try{
            $response = CurlToolkit::request("GET",
                sprintf(self::OAUTH_OPENID_URL.'?access_token=%s', $accessToken));

            if(strpos($response, "callback") !== false){
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response = substr($response, $lpos + 1, $rpos - $lpos -1);
            }

            $user = json_decode($response, true);
            if(isset($user['error'])){
                throw new AuthenticationException(
                    sprintf('GET %s AccessToken failure errcode:%s errmsg:%s',
                        self::PROVIDER_ID , $user['error'], $user['error_description'])
                );
            }

            return $user;
        }catch (\Exception $ex){
            throw new AuthenticationException(
                sprintf('GET %s OpenId failure error:%s',self::PROVIDER_ID , $ex)
            );
        }
    }

    private function getQqUserInfo($accessToken, $openId){
        try{
            $url = sprintf(self::OAUTH_USER_INFO_URL."?access_token=%s&oauth_consumer_key=%s&openid=%s",
                $accessToken, $this->appId, $openId);

            $info = json_decode(CurlToolkit::request("GET", $url), true);

            if(!isset($info['ret']) || $info['ret'] < 0){
                throw new AuthenticationException(
                    sprintf('GET %s UserInfo failure error:%s',self::PROVIDER_ID, $info['msg'])
                );
            }

            return $info;
        }catch (\Exception $ex){
            throw new AuthenticationException(
                sprintf('GET %s UserInfo failure error:%s',self::PROVIDER_ID , $ex)
            );
        }
    }

    private function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH){
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * @return Request
     */
    private function getRequest(){
        return $this->container->get('request');
    }

    private function getRequestGetHasAttribute($name){
        return $this->getRequest()->query->has($name);
    }

    private function getRequestGetAttribute($name){
        return $this->getRequest()->query->get($name);
    }

    /**
     * @return Biz
     */
    private function getBiz(){
        return $this->container->get('biz');
    }

    /**
     * @return UserconnectionService
     */
    private function getUserConnectionService(){
        return $this->getBiz()->service("User:UserconnectionService");
    }

}