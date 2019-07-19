<?php
namespace app\home\controller;
use app\home\controller\Base;
use think\Session;
class Article extends Base
{
	public function index(){
    	return $this->category();
    }
    public function ac(){
        $this->display();
    }
    public function category(){
    	/*$cid=intval($_REQUEST['cid']);
		$pagetitle=GTC($cid,'Cate');
    	$pagetitle=empty($pagetitle) ? '文章列表' : $pagetitle;
        $this->setseoinfo($pagetitle,$pagetitle,$pagetitle);
		*/
        return view('index');
    }
    public function read($id=0){
        return view('view',['id'=>$id]);
        //M('Article')->where('id='.$id)->setInc('hit');
    }
    public function search($qw=''){
        $qw= empty($qw) ? input('qw') : $qw;
        $this->setseoinfo('搜索结果:'.$qw,$qw,$qw);
        return view('index');
    }
    public function edit($id=0){
        //$data = input('param.');
        parent::checkuser();
        if(request()->isPost()){
            $ret = parent::save();
            P($ret);
            if($ret===false){
                $this->error('保存失败!');
            }else{
                $this->saveTag(input('param.tags','','trim'),$id,'Article');
                $Ping=new \Net\Ping();
                //$Ping->allPing($id,GTC($id,'Article','cid'));
                $Ping->baidu($ret,GTC($ret,'Article','cid'));
                //$Ping->google($id,GTC($id,'Article','cid'));
                $this->success('保存成功!','/'.$id.'.html');
            }
        }else{
            return view('Article:edit',array(
                'id' => $id,
            ));
        }
    }
    function setbad(){
        M('Article')->setInc('bad');
    }
    function setgood(){
        M('Article')->setInc('good');
		$this->success('OK');
    }
	function dashang(){
		$param['order_no'] = substr(md5(time().print_r($_SERVER,1)), 0, 24); //订单号
		$param['channel'] = 'alipay'; //表单提交的方式支持  alipay  wxpay   chinapay_b2c  chinapay  需要哪个就替换哪个
		$param['amount'] = 0.01; //金额
		$param['subject'] = "打赏作者";//商品名
		$param['metadata'] = "";//备注
		$param['return_url'] = 'http://www.sssui.com/Pay/return.html';
		$param['notify_url'] = 'http://www.sssui.com/Pay/notify.html';//支付成功后天工支付网关通知
		$param['client_ip'] = get_client_ip();//'127.0.0.1';
		$param['client_id'] = $TEE_CLIENT_ID;

	}
    function getcomment(){
        $pid=I('get.pid',0,'intval');
        $p=I('get.p',1,'intval');
        $module=I('get.module','Article','trim');
        $pagesize=I('get.pagesize',20,'intval');

        $Comment=model('Comment');
        $count = $Comment->where('module="Article" and commentid=0 and pid='.$pid)->count('id');
        $ret=array(
            'total'=>$count,
            'page'=>$p,
            'total_page'=>ceil($count/$pagesize),
            'list'=>array()
        );
        $field='id,pid,commentid,url,content,nickname,FROM_UNIXTIME(create_time,"%Y-%m-%d %H:%i:%s") as create_time';
        if ($count > 0) {
            $firstRow= $pagesize*($p-1);
            $CommentList = $Comment->Field($field)->Where('module="Article" and commentid=0 and pid='.$pid)->Limit($firstRow.','.$pagesize)->Order('id desc')->Select();
            foreach ($CommentList as $key => $value) {
                $CommentList[$key]['_child']=$Comment->Field($field)->Where('module="Article" and commentid='.$value['id'].' and pid='.$pid)->Order('id desc')->Select();
            }
            $ret['list']=$CommentList;
        }
        echo json_encode($ret);
    }
    function preview(){
        $this->assign('vo',$_POST);
        $this->display('view');
    }
    function gettags(){
        $kw=input('kw');
        $Tag=model('Tag');
        if($kw){
            $list=$Tag->where('module="Article" and name like "%'.$kw.'%"')->limit(10)->order('name asc')->getField('name',true);
        }else{
            $list=$Tag->where('module="Article"')->limit(10)->order('name asc')->getField('name',true);
        }
        echo json_encode($list,JSON_UNESCAPED_UNICODE);
    }
    function getalbum(){
        $Attach = model('Attach');
        $p=input('get.p',1,'intval');
        $pagesize=input('get.pagesize',15,'intval');
        $map=array(
            'extension'=>array('in','jpg,png,gif,bmp')
        );
        $count = $Attach->where($map)->count('id');
        $ret=array(
            'total'=>$count,
            'page'=>$p,
            'total_page'=>ceil($count/$pagesize),
            'list'=>array()
        );
        $field='id,CONCAT(savepath,savename) as src';
        if ($count > 0) {
            $firstRow= $pagesize*($p-1);
            $ret['list'] = $Attach->Field($field)->Where($map)->Limit($firstRow.','.$pagesize)->Order('id desc')->Select();
        }
        echo json_encode($ret);
    }
    function catchToQiniu(){
        $ret= catchToQiniu($_POST['srcs'],$_POST['path']);
        echo json_encode($ret,true);
    }
	function autosave(){
		$_POST['id']=-1;
        $ret=parent::update();
		$this->success('ok');
	}
	/*按标签显示文章*/
    function tag(){
        return view('index',['tag'=>input('tag')]);
    }
}
