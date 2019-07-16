<?php
// +----------------------------------------------------------------------
// | 公用模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;
use think\Model;
class Base extends Model {

	// 获取当前用户的ID
    public function getMemberId() {
        return isset($_SESSION[Config('USER_AUTH_KEY')])?$_SESSION[Config('USER_AUTH_KEY')]:0;
    }
	public function checkTitle($title=null,$id=null) {
        $title=is_null($title) ? $_POST['title'] : $title;
        $id=is_null($id) ? $_POST['id'] : $id;
        if(is_string($title)) {
            $map['title']	 =	 $title;
            if(!empty($id)) {
                $map['id']	=	array('neq',$id);
            }
            $result	=	$this->where($map)->count('id');
            if($result>0) {
                return false;
            }else{
                return true;
            }
        }
        return true;
	}
	public function checkName($name=null,$id=null) {
        $name=is_null($name) ? $_POST['name'] : $name;
        $id=is_null($id) ? $_POST['id'] : $id;
        if(is_string($name)) {
            $map['title']	 =	 $name;
            if(!empty($_POST['id'])) {
                $map['id']	=	array('neq',$id);
            }
            $result	=	$this->where($map)->count('id');
            if($result>0) {
                return false;
            }else{
                return true;
            }
        }
        return true;
	}
    function getList($data=array()){
        $limit = $data['limit'];unset($data['limit']);
        $order = $data['order'];unset($data['order']);
        $pagesize = $data['pagesize'] ? $data['pagesize'] : Config('PAGE_SIZE');unset($data['pagesize']);
        if(method_exists($this, '_fileter')){
            $data=$this->_fileter($data);            
        }
        if($limit){
            if($limit=='all'){
                $list=$this->Where($data)->Order($order)->Select();
            }else{
                $list=$this->Where($data)->Limit($limit)->Order($order)->Select();             
            }
			$page ="";
            //echo $this->getlastsql();
        }else{
			$list = $this->Where($data)->Order($order)->paginate();
			$page = $list->render();
			//echo $this->getlastsql();
        }
        $var=  array(
            'list'      =>  $list,
            'page'      =>  $page,
            'pages'     =>  array(
                'total'     =>  $count,
                'page'      =>  $p->nowPage,
                'pagesize'  =>  $pagesize
            )
        );
        return $var;
    }
    function getView($data=array()){
        $var=$this->Where($data)->Order($order)->Find();
        return $var;
    }

}
?>