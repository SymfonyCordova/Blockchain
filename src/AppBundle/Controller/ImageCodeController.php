<?php
namespace AppBundle\Controller;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageCodeController extends BaseController
{
    public function captchaAction(Request $request){
        $width = $request->query->getInt('width') ?: 150;  // 验证码图片的宽度
        $height = $request->query->getInt('height') ?: 50;  // 验证码图片的高度
        $length = 5;  // 验证码字符的长度
        $effect = true;  // 是否忽略验证图片上的干扰线条

        $pharse = new PhraseBuilder();
        $captcha = new CaptchaBuilder($pharse->build($length));
        $image = $captcha->setIgnoreAllEffects($effect)->build($width, $height)->get();

        $session = $request->getSession();
        $session->set('captcha', $captcha->getPhrase());

        $response = new Response($image);
        $response->headers->set('content-type', 'image/jpeg');
        $response->headers->set('Expires', 0);
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->headers->set('Cache-Control', 'post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}