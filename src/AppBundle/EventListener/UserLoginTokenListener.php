<?php
namespace AppBundle\EventListener;


use Biz\User\Service\UserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Firewall;

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

        if($session->getId() != $user['login_session_id']){
            $session->invalidate();

            $this->container->get('security.token_storage')->setToken(null);

            $goto = $this->generateUrl("login");

            $response = new RedirectResponse($goto, '302');

            setcookie('REMEMBERME', '', -1);

            $this->container->get('session')->getFlashBag()->add('danger', '此帐号已在别处登录，请重新登录');

            $event->setResponse($response);
        }
    }



    /**
     * @return UserService
     */
    private function getUserService(){
        return $this->createService("User:UserService");
    }

    private function getUser()
    {
        $biz = $this->getBiz();

        return $biz['user'];
    }

}