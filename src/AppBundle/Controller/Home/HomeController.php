<?php
namespace AppBundle\Controller\Home;

use AppBundle\Controller\BaseController;
use Biz\User\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends BaseController
{
    public function indexAction(Request $request){
        //return new JsonResponse($this->generateUrl('phone_code_login'));
        return $this->render("AppBundle:Home:index.html.twig", array());
    }

    public function gpAction(){
        return $this->render("AppBundle:Home:gp.html.twig", array());
    }

    /**
     * @return UserService
     */
    public function getUserService(){
        return $this->createService("User:UserService");
    }
}