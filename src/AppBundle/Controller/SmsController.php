<?php
namespace AppBundle\Controller;

use AppBundle\Common\StringToolkit;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SmsController extends BaseController
{
    public function sendAction(Request $request){
        $mobile = $request->get("mobile");
        $key = $this->getParameter("mobile.secret").$mobile;
        $code = StringToolkit::createRandomNumber(4);
        $this->getRedis()->set($key, $code, 60);
        return $this->redirect($this->generateUrl('login'));
    }
}