<?php
namespace AppBundle\Controller;

class PhoneController extends BaseController{
    public function login(){
        return $this->redirect($this->generateUrl('login'));
    }
}