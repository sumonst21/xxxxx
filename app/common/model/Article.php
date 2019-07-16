<?php
// +----------------------------------------------------------------------
// | 文章模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use app\common\model\Base;

class Article extends Base {
    protected $pk = 'id';
    protected $insert = ['title','create_time','content','status' => 1];  
    protected $update = ['title','update_time','content',];  
	protected function setCreateTimeAttr()
    {
        return time();
    }
	protected function setUpdateTimeAttr()
    {
        return time();
    }
	/*protected function setTitleAttr($value,$data)
    {
        return htmlentities($value);
    }*/
	protected function setContentAttr($value,$data)
    {
		$value=str_replace("<br />","",$value); 
		//$value=str_replace("\r","\\n",$value); 
		//$value=str_replace("\r\n","\\n",$value); 
		return $value;
        //return fetchImgToQiniu($value);
    }
   	/*protected function setAbstructAttr($value,$data){
    	return $value ? $value : utf8Substr(trim(strip_tags($data['content'])),0,250);
    }*/

	function _filter($data=array()){
		$data['id']=array('gt',0);
		if(empty($data['cid'])){
			unset($data['cid']);			
		}
		if(array_key_exists('keywords',$data) && !empty($data['keywords'])){			
			$kws=explode(' ',$data['keywords']);
			if (count($kws)>0){
				$m=array();
				foreach ($kws as $k => $v) {
					$m[]="%".$v."%";
				}
				$data['title'] = array('like',$m,'OR');
			}
		}
		unset($data['keywords']);
		if(array_key_exists('tag',$data) && !empty($data['tag'])){
			if(!empty($data['tag'])){
				$data['tag']=trim($data['tag']);
				$Tag = model("Tag");
				$Stat  = $Tag->where('module="Article" and name="'.$data['tag'].'"')->field("id,count")->find();
				$tagId  =  $Stat['id'];
				$count   =  $Stat['count'];
				$Tagged = model("Tagged");
				$recordIds  =  $Tagged->where("module='Article' and tag_id=".$tagId)->column('id,record_id');
				if($recordIds) {
					$data['id']   = array('IN',$recordIds);
				}
			}
		}
		unset($data['tag']);
		return $data;
	}
	function getPrevOne($data=array()){
		//echo $data['id'];
		if(empty($data['id'])) return false;
		$vo=$this->where('id>0 and id<'.$data['id'])->Order('id desc')->find();
		//echo $this->getlastsql();
		return $vo;
	}
	function getNextOne($data=array()){
		if(empty($data['id'])) return false;
		$vo=$this->where('id>0 and id>'.$data['id'])->Order('id asc')->find();
		return $vo;
	}
}
?>