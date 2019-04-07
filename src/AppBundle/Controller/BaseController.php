<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends Controller
{
    protected function createJsonResponse($data = '', $message = '', $errorCode = 0)
    {
        return new JsonResponse(array(
            'data' => $data,
            'errmsg' => $message,
            'errcode' => $errorCode === false ? 1 : $errorCode,
        ));
    }

    /**
     * @return \Codeages\Biz\Framework\Context\Biz
     */
    protected function getBiz()
    {
        return $this->container->get('biz');
    }

    protected function createService($alias)
    {
        $biz = $this->getBiz();

        return $biz->service($alias);
    }

    /**
     * @return \Redis
     */
    protected function getRedis(){
        $biz = $this->getBiz();

        return $biz['redis'];
    }
}