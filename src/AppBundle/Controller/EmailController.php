<?php
/**
 * Created by PhpStorm.
 * User: symfony
 * Date: 2019/4/3
 * Time: 17:18
 */

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Request;

class EmailController extends BaseController{

    public function sendEmailAction(Request $request){
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('XXXXXXXX@163.com') //在配置文件中配置的邮箱，发送方
            ->setTo('recipient@qq.com')   //接收方
            ->setBody(
                $this->renderView(
                // app/Resources/views/default/email.html.twig
                    'default/email.html.twig',
                    array()
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);

        return $this->render('default/index.html.twig', array());
    }

}