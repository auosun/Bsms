<?php

namespace app\admin\model;
use think\Model;
use think\Paginator;

class Activity extends Model
{
    protected $dataFormat = 'Y/m/d';

    protected $createTime = 'create_time';

    protected $updateTime = 'update_time';

    protected $autoWriteTimestamp = true;

    public function getstatusAttr($value)
    {
        $status = [
            -1=>'尚未开始',
            0=> '进行中',
            1=>'已结束',
        ];
        return $status[$value];
    }

    public function getStartTimeAttr($value)
    {
        if($value){
            return date('Y/m/d H:i', $value);
        }else{
            return '尚未开始';
        }
    }

    public function getOverTimeAttr($value)
    {
        if($value){
            return date('Y/m/d H:i', $value);
        }else{
            return '尚未结束';
        }
    }


}