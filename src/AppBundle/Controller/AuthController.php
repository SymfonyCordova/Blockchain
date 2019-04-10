<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Codeages\Biz\Framework\Session\Handler\BizSessionHandler;
use Redis;

class AuthController extends BaseController{
    public function qqAction(Request $request){

    }

    public function qqRegisterAction(Request $request){
        //todo post 请求绑定qq用户 以及解除绑定
        $params = $request->attributes->all();
        return $this->render("AppBundle:default:bind.html.twig", $params);
    }
}