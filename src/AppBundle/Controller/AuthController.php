<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class AuthController extends BaseController{
    public function qqAction(Request $request){
    }

    public function qqRegisterAction(Request $request){
        $params = $request->attributes->all();
        return $this->render("AppBundle:default:bind.html.twig", $params);
    }
}