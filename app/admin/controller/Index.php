<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Index extends Base
{
    public function index()
    {
        $uid = Session(Config('USER_AUTH_KEY'));
        $this->getMenu();
        return view('index',['menu'=>Session('menu'.$uid)]);
        /*$key = Config('USER_AUTH_KEY');
        if(Session('?'.$key)) {
            $uid = Session($key);
            $this->getMenu();
            return view('index',['menu'=>Session('menu'.$uid)]);
        }else{
            $this->error('请先登录！',__APP__.'/Member/login.html');
        }*/
    }
    public function test()
    {
        return view('',['img'=>'/static/api/images/thumb_220_220_8f28a19f7cf312a0021908c612b2f601.jpg']);
    }
    public function getMenu($Refresh=false){
        $key = Config('USER_AUTH_KEY');
        $uid = Session($key);

        //显示菜单项
        if($Refresh || !Session('?menu'.$uid)) {
            $RoleId = model('Access')->where('uid='.$uid)->value('group_id');
            $Rules = model('Role')->where('id='.$RoleId)->value('rules');
            //$Rules = model('Role')->query('SELECT o.`rules` FROM `z_role` as o JOIN `z_access` as g on g.id=o.group_id WHERE g.uid='.$uid)->value('rules');
            //读取数据库模块列表生成菜单项
            $Rule = model("Rule");
            $RuleList   =   $Rule->where([
                ['id','in',is_string($Rules) ? $Rules : implode(',',$Rules)],
                ['status','=','1'],
                ['is_show','=','1'],
                ['is_menu','=','1']
            ])->Field('id,name,title,group_id,condition')->select();
            //echo $Rule->getlastsql();
            $Roles  =   model('App')->order('sort asc')->column('id,name,icon,title,is_show,status');
            //P('in('.(is_string($Rules) ? $Rules : implode(',',$Rules)).')');exit();
            foreach($Roles as $k=>$v) {
                $Roles[$k]['child']=[];
            }
            foreach($RuleList as $k=>$v) {
                if($v['group_id']!=0){$Roles[$v['group_id']]['child'][]=$v;}

                /*if(Session('?administrator')){

                }*/
            }
            //缓存菜单访问
            Session('menu'.$uid,$Roles);
        }
    }
    public function welcome()
    {
        return view();
    }
    public function clear(){
        function getfiles($path){ 
            foreach(scandir($path) as $fn){
                if($fn=='.'||$fn=='..') continue; 
                if(is_dir($path.'/'.$fn)){ 
                    getfiles($path.'/'.$fn); 
                } else { 
                    //echo $path.'/'.$fn.'<br />'; 
                    unlink($path.'/'.$fn);
                } 
            } 
        } 
        //简单的demo,列出当前目录下所有的文件
        getfiles(\Env::get('runtime_path') . 'log/');
        getfiles(\Env::get('runtime_path') . 'cache/');
        getfiles(\Env::get('runtime_path') . 'temp/');
        ////前台用ajax get方式进行提交的，这里是先判断一下
        //if($_POST['type']){
        $this->success('清除成功',$_SERVER['HTTP_REFERER']);
        //}else{
        //  $this->display();
        //}
    }
}
