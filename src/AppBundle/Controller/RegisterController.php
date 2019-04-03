<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class RegisterController extends BaseController
{
    public function indexAction(Request $request)
    {
        if($request->getMethod() == "POST"){
            $fields = $request->request->all();
            $this->getUserService()->createUser($fields);
            return $this->redirectToRoute('login');
        }

        return $this->render("AppBundle:default:register.html.twig");
    }

    /**
     * @return \Biz\User\Service\UserService
     */
    protected function getUserService()
    {
        return $this->createService("User:UserService");
    }
}