<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Member extends Base
{
    public function quickmenu(){
        $id= input('id');
        $model = model('User');
        if(is_numeric($id)){
            echo 'number';
        }else{
            echo 'Admin/'.$id.'/index';
        }
    }
    public function login()
    {
        if(request()->isPost()){
            if(empty($_POST['account'])) {
                $this->error('帐号错误！');
            }elseif (empty($_POST['password'])){
                $this->error('密码必须！');
            }
            //生成认证条件
            $map            =   array();
            // 支持使用绑定帐号登录
            $User	=	model('AdminUser');
            $vo = $User->where('account',Input('post.account'))->where('status','>',0)->find();
            if(false === $vo) {
                $this->error('帐号不存在或已禁用！');
            }else {
                if($vo['password'] != md5($_POST['password'])) {
                    $this->error('密码错误！');
                }
                //Cookie('loginfo',authcode($vo['id'].','.$vo['nickname'].','.$vo['account'],'ENCODE'));
                Session(Config('USER_AUTH_KEY'),$vo['id']);
                Session('nickname',$vo['nickname'] ? $vo['nickname'] : $vo['account']);
                if($vo['id']==261) {
                    Session(Config('ADMIN_AUTH_KEY'),true);
                }
                //保存登录信息;
                $data = array();
                $data['last_login_time']	=	time();
                $data['last_login_ip']	=	request()->ip();
                $User->where('id',$vo['id'])->inc('login_count',1)->data($data)->update();
                $this->success('登录成功！',__APP__.'/');
            }
        }else{
            return view('base:login');
        }
    }

    // 验证码显示
    public function verify()
    {
        //
        //Image::buildImageVerify(4,1, 'png', 290, 130, 'TestVerify');
        import("ORG.Util.Verify");
        $Verify=new Verify();
        $Verify->entry('Login');
    }
    public function logout(){
        $jumpUrl='/';
        if(session('?'.Config('USER_AUTH_KEY'))) {
            session(Config('USER_AUTH_KEY'),null);
            session(Config('ADMIN_AUTH_KEY'),null);
            session('nickname',null);
            session_destroy();
            Cookie('loginfo',null);
            $this->success('登出成功！',$jumpUrl);
        }else {
            $this->success('已经登出！',$jumpUrl);
        }
    }
}
