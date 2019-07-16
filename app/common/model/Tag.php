<?php
// +----------------------------------------------------------------------
// | 标签模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use app\common\model\Base;
class Tag extends Base {
	protected $_validate	 =	 array(
			array('name','require','请输入标签名称！'),
	);
    function _filter($data=array()){
        $data['id']=array('gt',0);
        if(!empty($data['keywords'])){          
            $kws=explode(' ',$data['keywords']);
            if (count($kws)>0){
                $m=array();
                foreach ($kws as $k => $v) {
                    $m[]="%".$v."%";
                }
                $data['name'] = array('like',$m,'OR');
            }
        }
        unset($data['keywords']);
        return $data;
    }
}
?>