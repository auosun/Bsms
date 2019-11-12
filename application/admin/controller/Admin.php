<?php

namespace app\admin\controller;
use think\Cookie;
use think\Request;
use think\Session;
use app\admin\model\Admin as AdminModel;

class Admin extends Base {

    /**
     *
     * 管理员界面渲染和功能
     * 1. login() 登录界面渲染
     * 2. info() 当前管理员用户资料界面渲染
     * ------
     * 管理员界面功能
     * a. checkLogin() 管理员用户登录验证
     * b. logout() 管理员用户退出后台
     *
     * @return string
     * @throws \think\Exception
     */


    /*
     * 管理员登录界面渲染
     * 获取存储在本地的 thinkAdmin_开头的Cookie 发现发送到 login页面
     */
    public function login(){
        $this->alreadyLogin();
        $this->view->assign('title','登录');
        if (Cookie::has('email','thinkAdmin_')&&Cookie::has('password','thinkAdmin_')){
            $loginInfo = array(
                "email"=>Cookie::get('email','thinkAdmin_'),
                "password"=>Cookie::get('password','thinkAdmin_'),
                "checked"=>"checked",
            );
            $this->view->assign('loginInfo',$loginInfo);
        }
        return $this->view->fetch('admin@admin/login');
    }

    /*
     * 渲染用户资料页面
     * 获取当前用户的所有资料并发送到 info界面
     */
    public function info()
    {
        $this->view->assign('title','个人资料');

        $adminUser = AdminModel::get(['id' => ADMIN_ID,]);
        $this->view->assign('adminUser',$adminUser);

        return $this->view->fetch('admin@admin/info');
    }


/* ------------------ */


    //登录验证
    public function checkLogin(Request $request){
        $loginData = $request->param();
        $email = $loginData['email'];
        $password = $loginData['password'];
        $status = 0;
        $result = '验证失败';
        $rule = [
            'email|邮箱' => 'require',
            'password|密码' => 'require'
        ];

        $result = $this->validate($loginData,$rule);

        if(true === $result){
            //查询条件
            $map = [
                'email' => $loginData['email'],
                'password' => md5($loginData['password']),
            ];
            //数据表查询 返回模型对象
            $user = AdminModel::get($map);

            if ($user === null){
                $result = '账号或密码，你总归错了一个歪';
            }else{
                $status = 1;
                $result = '验证通过，点击[确定]进入后台';

                Session::set('admin_id',$user->id);
                Session::set('admin_info',$user->getData());

                if ($loginData['saveLogin']){
                    Cookie::init(['prefix'=>'thinkAdmin_','expire'=>864000]);
                    Cookie::prefix('thinkAdmin_');
                    Cookie::set('email',$loginData['email']);
                    Cookie::set('password',$loginData['password']);
                }else{
                    Cookie::clear('thinkAdmin_');
                }

                $user->setInc('login_count');
            }
        }
        return ['status' => $status,'message'=>$result];
    }

    //退出登录
    public function logout()
    {
        AdminModel::update(['login_time' => time()],['id' => Session::get('admin_id')]);
        Session::delete('admin_id');
        Session::delete('admin_info');
        $this->success('注销登录,正在返回',url('admin/login'));
    }

    //更新管理员用户资料
    public function updateInfo(Request $request){
        $adminUserInfo = $request->param();
        //丢掉用户资料中无效项
        foreach ($adminUserInfo as $key=>$value)
        {
            if ($value=='')
            {
                unset($adminUserInfo[$key]);
            }
        }
        AdminModel::update($adminUserInfo,['id'=>Session::get('admin_id')]);
        $this->redirect('admin/info');
    }

    //更新管理员用户头像
    public function uploadImg(Request $request){
        $status = 0;
        $result = '未知错误！';
        $file = $request->file('image');
        $info = $file->validate(['file' => 'require','size'=>2097152,'ext'=>'jpg,png,gif'])->move(ROOT_PATH.'public'.DS.'uploads');
        if ($info)
        {
            $avatar = '/uploads/'.$info->getSaveName();
            AdminModel::update(['avatar'=>$avatar],['id'=>Session::get('admin_id')]);
            $result = "图片上传成功。";
            $status = 1;
        }
        else
        {
            $result = $file->getError();
        }
        return ['status' => $status,'message'=>$result];
    }

    //删除管理员头像
    public function deleteImg(){
        $gravatar = 'https://s.gravatar.com/avatar/'.md5(Session::get('admin_info')['email']).'?s=100&r=g&d=mp';
        AdminModel::update(['avatar'=>$gravatar],['id'=>Session::get('admin_id')]);
    }
}