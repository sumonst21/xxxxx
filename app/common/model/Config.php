<?php
namespace app\common\model;
use app\common\model\Base;
class Config extends Base
{
    protected $auto = [];
    protected $insert = ['create_time'];
    protected $update = ['update_time'];

    public function setCreateTimeAttr($value,$data)
    {
        return time();
    }

    public function setUpdateTimeAttr($value,$data)
    {
        return time();
    }
}
