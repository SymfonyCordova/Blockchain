<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Controller\BaseController;
use Biz\User\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends BaseController
{
    public function indexAction(){
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();

        return  new JsonResponse(array("password"=>$user['password'], "salt"=>$user['salt']));
    }

    /**
     * @return UserService
     */
    public function getUserService(){
        return $this->createService("User:UserService");
    }

}