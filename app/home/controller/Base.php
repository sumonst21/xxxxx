<?php
// +----------------------------------------------------------------------
// | Home项目基类
// +----------------------------------------------------------------------
// | Home所有Action都继承此类
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
//import("ORG.Util.Cookie");

namespace app\home\controller;
//use think\View;
use think\Controller;
use ORG\Safe;
class Base extends Controller
{
    function initialize() {$this->_initialize();}
    function _initialize() {
        /*if(Safe::check(true)){
            $this->error('您的提交带有不合法参数,谢谢合作!');
        }*/
        if (!session('?uid')){
			$_SESSION['uid']=Config('GUEST_AUTH_ID');
			$_SESSION['nickname']='游客';
			$_SESSION['account']='游客';
        }
        //$this->navigation();
        $this->readConfig();
        //Config('template.layout_on',true);
        //Config('template.view_path',\Env::get('root_path').'tpl/');
    }
    protected function readConfig(){
        $list=Cache('Config');
        if (!$list) {
            $config = model("Config");
            $groups = $config->distinct(true)->column('category');
            $list = array();
            foreach ($groups as $value){
                $_tmp = $config->where('category',$value)->column('name,value');
                $list[$value] = array_change_key_case($_tmp,CASE_LOWER);
            }
            $list=array_change_key_case($list,CASE_LOWER);
            // 所有配置参数统一为大写
            Cache('Config',$list);
        }
        foreach ($list as $key=>$value){
            Config($value,$key);
        }
    }
    protected function navigation(){
        $list=Cache('Navigation');
        if (!$list) {
            $Menu = model('Menu');
            $list = $Menu->fielmodel('id,name,link,title,target')->where('status=1 and level=0')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                $list[$key]['_child']=$Menu->fielmodel('name,link,title,target')->where('status=1 and level=1 and pid='.$value['id'])->order('sort asc')->select();
            }
			$list=array_change_key_case($list,CASE_UPPER);
            Cache('Navigation',$list);
        }
        $this->assign('navigation', $list);
    }
    // 404 错误定向
    function http404($message = '', $jumpUrl = '', $waitSecond = 3) {
        $this->assign('message', '访问的页面不存在！');
        if (!empty($jumpUrl)) {
            $this->assign('jumpUrl', $jumpUrl);
            $this->assign('waitSecond', $waitSecond);
        }
        return view('Public:http404');
    }
    public function _empty($method) {
        return view();
    }
    public function save($modelName=false,$data=null)
    {
        if (!$modelName){
            $modelName=request()->controller();
        }
        if (is_null($data)){
            $data=Input('post.');
        }
        $result = $this->validate($data,'app\\common\\validate\\'.$modelName);
        if (true !== $result) {
            $this->error($result);
            //return $result;
        }
        $model  =   model($modelName);
        $pk = $model->getPk();
        if($data[$pk]){
            $map = [];
            $map[$model->getPk()]=$data[$pk];
            $result = $model->allowField(true)->save($data,$map);
            $model->id = $data[$pk];
            //echo 'update',$result;
            //echo $model->getlastsql();
        }else{
            $result = $model->allowField(true)->save($data);
            //echo 'insert',$result;
        }
        if($result) {
            return $model->id;
        }else {
            return false;
        }
    }
    function postcomment(){
        $Comment=model('Comment');
        if(false === $Comment->create()) {
            $this->error($Comment->getError());
        }
        if($result = $Comment->add()) {
            $this->assign('jumpUrl',$_POST['jumpurl']);
            $this->success('评论成功');
        }else {
            $this->error('评论失败');
        }
    }
    function getcomment(){
        $pid=input('pid');
        $currpage=input('p');
        $module=input('module');
        $model=model('Comment');
        $map="status=1 and module='".$module."' and pid=".$pid;
        $count = $model->where($map)->count('id');
        if ($count > 0) {
            $list = $model->where($map)->order('id desc ')->page($currpage,20)->select();
            foreach ($list as $key => $vo) {
                $blogurl=U('Blog/').$vo['uid'];
                $cui=GUI($vo['uid']);
                $html.='<li><a href="'.$blogurl.'"><img src="'.$cui['avatar'].'" alt="'.$cui['nickname'].'" /></a><a href="'.$blogurl.'" class="green">'.$cui['nickname'].'</a><span class="colorL">'.toDate($vo['create_time'],'Y-m-d H:i:s').'</span><div class="colorD">'.parseemote($vo['content']).'</div></li>';
            }
            $this->success($html);
        }else{
            $this->error('木有内容');
        }
    }
    function checkuser(){
        if (!session('uid')){
            redirect('/Login.html');
        }
    }
	function setseoinfo($title='',$kw='',$description='',$addr=array()){
		$keyword 			=	require(\Env::get('config_path').'keyword.php');
        $this->assign('page_title', $title);
        $this->assign('page_keyword', $kw);
        $this->assign('page_description', $description);
	}
	function saveattach(){
		save_attach($_POST);
	}
    // 验证码显示
    public function verify()
    {
        config('app_trace',false);
        //Image::buildImageVerify(4,1, 'png', 290, 130, 'TestVerify');
        //import("ORG.Util.Verify");
        $Code=new \ORG\Verify\Code();
        return $Code->send(Input('get.name'));
    }
}
