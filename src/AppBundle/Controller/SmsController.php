<?php
namespace AppBundle\Controller;

use AppBundle\Common\StringToolkit;
use Symfony\Component\HttpFoundation\Request;

class SmsController extends BaseController
{
    public function sendAction(Request $request){
        $mobile = $request->get("mobile");
        $key = $this->getParameter("mobile.secret").$mobile;
        $code = StringToolkit::createRandomNumber(4);
        if($this->getRedis()->get("$key")){
            return $this->createJsonResponse('', '发送短信过于频繁', false);
        }
        $this->getRedis()->set($key, $code, 60);
        //todo接入第三方短信接口
        return $this->createJsonResponse();
    }
}