<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->view->assign('title','登陆');
        return $this->view->fetch('admin@user/login');


    }
}
