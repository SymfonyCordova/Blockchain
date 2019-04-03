<?php
namespace AppBundle\EventListener;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener extends BaseListener
{
    public function onKernelException(GetResponseForExceptionEvent $event){
        $exception = $event->getException();
        $request = $event->getRequest();

        if($this->inUrlWhiteList($request->getPathInfo())){
            return true;
        }

        $isDebug = $this->container->getParameter("kernel.debug");

        //判断是否是ajax请求
        if ($request->isXmlHttpRequest()) {
            $error = array(
                'data' => false,
                'errmsg' => $exception->getMessage(),
                'errcode' => $exception->getCode(),
            );

            //开发者模式 添加错误信息
            if ($isDebug) {
                $error['previous'] = $this->getPreviousErrors($exception);
            }

            $event->setResponse(new JsonResponse($error), 500);
        }

        //不是开发者模式
        if(!$isDebug){
            $response = $this->container->getParameter('templating')
                ->renderResponse('error.html.twig', array(
                    'errmsg'=>$exception->getMessage(),
                    'errcode' => $exception->getCode(),
                ));
            $response->setStatusCode(500);
            $event->setResponse($response);
        }

    }

    protected function getPreviousErrors($exception)
    {
        $previousErrors = array();

        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }

        $flags = PHP_VERSION_ID >= 50400 ? ENT_QUOTES | ENT_SUBSTITUTE : ENT_QUOTES;

        $count = count($exception->getAllPrevious());
        $total = $count + 1;
        foreach ($exception->toArray() as $position => $e) {
            $previous = array();

            $ind = $count - $position + 1;

            $previous['message'] = "{$ind}/{$total} {$e['class']}: {$e['message']}";
            $previous['trace'] = array();

            foreach ($e['trace'] as $position => $trace) {
                $content = sprintf('%s. ', $position + 1);
                if ($trace['function']) {
                    $content .= sprintf('at %s%s%s(%s)', $trace['class'], $trace['type'], $trace['function'], '...args...');
                }
                if (isset($trace['file']) && isset($trace['line'])) {
                    $content .= sprintf(' in %s line %d', htmlspecialchars($trace['file'], $flags, 'UTF-8'), $trace['line']);
                }

                $previous['trace'][] = $content;
            }

            $previousErrors[] = $previous;
        }

        return $previousErrors;
    }
}