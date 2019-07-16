<?php
namespace app\common\model;
use app\common\model\Base;
class Role extends Base
{
    /*protected $insert = ['rules'];
    protected $update = ['rules'];*/
    public function getRulesAttr($value,$data)
    {
        if(empty($value)){
            return array();
        }
        if(is_string($value)){
            return explode(',',$value);
        }
    }

    /*public function setRulesAttr($value,$data)
    {
        if(empty($value)){
            unset($data['rules']);
        }
        if(is_array($value)){
            $value = implode(',',$value);
            return $value;
        }
    }*/
	protected function _filter($data){
		if($data['keywords']){
			$data['title'] = array('like','%'.$data['keywords'].'%');
		}
		unset($data['keywords']);
		return $data;
	}
}
