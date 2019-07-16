<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Attach extends Base
{
	function fm(){
		return view('fileManage');
	}
	public function getList(){
        $type=Input('get.type','image','trim');
        $Attachment = model('Attach');
        /*$p=I('get.p',1,'intval');
        $pagesize=I('get.pagesize',15,'intval');*/
        $typeArr=[
            'image'=>'jpg,png,gif,bmp,jpeg',
            'file'=>'jpg,png,gif,bmp,exe,rar,php,txt,json,js,css,zip,wma,fla,mp4,mp3',
        ];
        $map=[
			['extension','in',$typeArr[$type]]
        ];
        /*$count = $Attachment->where($map)->count('id');
        //echo $Attachment->getlastsql();
        $ret=array(
            'total'=>$count,
            'page'=>$p,
            'total_page'=>ceil($count/$pagesize),
            'list'=>array()
        );*/
        $field='id,CONCAT("'.__APP__.'",savename) as url,name,CONCAT("'.__APP__.'",savename) as thumb';
        /*if ($count > 0) {
            $firstRow= $pagesize*($p-1);
            $ret['list'] = $Attachment->Field($field)->Where($map)->Limit($firstRow.','.$pagesize)->Order('id desc')->Select();
        }*/
        $ret = $Attachment->Field($field)->Where($map)->Limit('0,300')->Order('id desc')->Select();
        echo json_encode($ret);
    }
    public function upload(){
        if($_FILES['file']){
            $file = request()->file('file');
            // 移动到框架应用根目录/uploads/ 目录下
            $savepath=Input('post.savepath','/Uploads/','trim');
            $info = $file->rule('uniqid')->move('.'.$savepath);
            if($info){
                $Attach = model('Attach');
                $Attach->name       = $info->getFilename();
                $Attach->type       = $info->getMime();
                $Attach->size       = $info->getSize();
                $Attach->extension  = $info->getExtension();
                $Attach->savepath   = $savepath;
                $Attach->savename   = str_replace('\\', '/',$info->getSaveName());
                $Attach->module     = '';
                $Attach->record_id  = 0;
                $Attach->hash       = $info->sha1();
                $Attach->pid        = 0;
                $Attach->remark     = '';
                $Attach->save();
                echo json_encode($Attach);
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            $this->error('未知文件');
        }
        //P($_FILES);
        //exit;
    }
    public function delete(){
        define('IS_AJAX',true);
        $id=I('id');
        $Attachment  =   model('Attach');
        if(isset($id)) {
            $condition = array('id'=>array('in',explode(',',$id)));
            $list =$Attachment->where($condition)->select();
            $total = count($list);
            $error=array();
            foreach ($list as $k => $v) {
                $Attachment->where('id='.$v['id'])->delete();
            }
            if(count($error) == $total){
                $this->error($error);
            }else{
                $this->success('删除成功');
            }
        }else {
            $this->error('非法操作');
        }
    }
}
