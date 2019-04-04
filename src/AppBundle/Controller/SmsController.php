<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SmsController extends BaseController
{
    public function sendAction(Request $request){
        //短信验证码的失效时间
        //短信验证码
        $result = $this->getRedis()->dbSize();
        return new JsonResponse(array('redis'=>$result));
    }
}