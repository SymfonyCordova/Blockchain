<?php
namespace AppBundle\EventListener;


use Biz\User\Service\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class UserLoginTokenListener extends BaseListener{

    public function onGetUserLoginListener(GetResponseEvent $event){
        if(HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()){
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        if(empty($session)){
            return;
        }

        $user = $this->getUser();
        if(!$user->isLogin()){
            return;
        }

        $user = $this->getUserService()->getUser($user['id']);
        $prefix = $this->container->getParameter('redis.prefix');

        if($prefix.$session->getId() != $user['login_session_id']){
            $session->invalidate();

            $this->container->get('security.token_storage')->setToken(null);

            $this->getRedis()->delete($prefix.$session->getId());

            $goto = $this->generateUrl("login");

            $response = new RedirectResponse($goto, '302');

            setcookie('REMEMBERME', '', -1);

            $event->setResponse($response);
        }
    }



    /**
     * @return UserService
     */
    private function getUserService(){
        return $this->createService("User:UserService");
    }

    /**
     * @return \Redis
     */
    private function getRedis(){
        $biz = $this->getBiz();

        return $biz['redis'];
    }

    private function getUser()
    {
        $biz = $this->getBiz();

        return $biz['user'];
    }

}