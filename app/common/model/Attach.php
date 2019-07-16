<?php
// +----------------------------------------------------------------------
// | 文章模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use app\common\model\Base;

class Attach extends Base {
    protected $insert = ['create_time','status' => 1,'download_count'=>0,'sort'=>0,'version'=>0,'verify'=>0,'user_id']; 
    protected $update = ['update_time']; 
	protected function setCreateTimeAttr()
    {
        return time();
    }
	protected function setUpdateTimeAttr()
    {
        return time();
    }
	protected function setUserIdAttr($v)
    {
        return $this->getUserId();
    }
}
?>