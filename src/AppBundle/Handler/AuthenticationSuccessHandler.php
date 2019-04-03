<?php

namespace AppBundle\Handler;

use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    private $logger;

    /**
     * AuthenticationSuccessHandler constructor.
     * @param $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        return parent::onAuthenticationSuccess($request, $token);
    }

}
