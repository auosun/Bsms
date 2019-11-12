<?php
namespace app\admin\model;
use think\Model;

class Admin extends Model{

    protected $insert = [
        'login_time' => NULL,
        'login_count' => 0,
    ];

    protected $update = [];

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

    protected $dateFormat = 'Y/m/d H:i';

    public function getRoleAttr($value){
        $role = [
            1=>'超级管理员',
            2=>'管理员',
        ];
        return $role[$value];
    }

    //密码修改器
    public function setPasswordAttr($value){
        return md5($value);
    }


    //登录时间获取器
    public function getLoginTimeAttr($value){
        return date('Y/m/d H:i',$value);
    }


}