<?php

namespace app\admin\controller;

use think\Request;
use think\Session;
use app\admin\model\Activity as ActivityModel;


class Index extends Base {

    //后台管理主页渲染
    public function index()
    {

        $this->view->assign('title','后台管理');

        //获取所有已进行过的活动
        $activities_last = ActivityModel::all(function($query){
            $query->where('status', 1);
        });

        //获取所有已进行过的活动参加的人数
        $activities_last_number = 0;
        foreach($activities_last as $key => $item)
        {
            $activities_last_number = $activities_last_number +  $item['number'];
        }

        //获取所有活动 根据创建时间排序 最新创建10个活动
        $activities_ten = ActivityModel::all(function ($query){
            $query->limit(10)->order('create_time', 'desc');
        });

        //10个最新创建的活动
        $this->view->assign('activities_ten',$activities_ten);
        //已开展活动数
        $this->view->assign('activities_last',count($activities_last));
        //已参加活动人数总和
        $this->view->assign('activities_last_number',$activities_last_number);
        //
        return $this->view->fetch('admin@index/index');
    }

}