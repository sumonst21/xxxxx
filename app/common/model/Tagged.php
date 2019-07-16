<?php
// +----------------------------------------------------------------------
// | 标签标记模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use app\common\model\Base;
class Tagged extends Base {
    protected $insert = ['create_time'];
	protected function setCreateTimeAttr()
    {
        return time();
    }
}
?>