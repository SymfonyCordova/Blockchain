<?php
namespace AppBundle\GuardAuthenticator;

use AppBundle\Common\CurlToolkit;
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

    const OAUTH_TOKEN_URL ='https://graph.qq.com/oauth2.0/token';

    const OAUTH_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    const PROVIDER_ID = 'QQ';

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

        if ($this->getRequestGetHasAttribute('code')) {
            return array('code' => $this->getRequestGetAttribute('code'));
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $accessToken = $this->getAccessToken($credentials);
        $openId = $this->getOpenId($accessToken);
        return $userProvider->loadUserByUsername($openId);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->generateUrl('homepage'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

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
            return $params["access_token"];
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

            return $user['openid'];
        }catch (\Exception $ex){
            throw new AuthenticationException(
                sprintf('GET %s OpenId failure error:%s',self::PROVIDER_ID , $ex)
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
}