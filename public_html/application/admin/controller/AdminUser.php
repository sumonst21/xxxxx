<?php
namespace app\admin\controller;

use app\common\model\AdminUser as AdminUserModel;
use app\common\model\AuthGroup as AuthGroupModel;
use app\common\model\AuthGroupAccess as AuthGroupAccessModel;
use app\common\controller\AdminBase;
use think\Config;
use think\Db;

/**
 * 管理员管理
 * Class AdminUser
 * @package app\admin\controller
 */
class AdminUser extends AdminBase
{
    protected $admin_user_model;
    protected $auth_group_model;
    protected $auth_group_access_model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->admin_user_model        = new AdminUserModel();
        $this->auth_group_model        = new AuthGroupModel();
        $this->auth_group_access_model = new AuthGroupAccessModel();
    }

    /**
     * 管理员管理
     * @return mixed
     */
    public function index()
    {
        $admin_user_list = $this->admin_user_model->paginate(10);

        return $this->fetch('index', ['admin_user_list' => $admin_user_list]);
    }

    /**
     * 添加管理员
     * @return mixed
     */
    public function add()
    {
        $auth_group_list = $this->auth_group_model->select();
        
        return $this->fetch('add', ['auth_group_list' => $auth_group_list]);
    }

    /**
     * 保存管理员
     * @param $group_id
     */
    public function save($group_id)
    {
        if ($this->request->isPost()) {
            $data            = $this->request->param();
            $validate_result = $this->validate($data, 'AdminUser');

            if ($validate_result !== true) {
                return json(array('code' => 0, 'msg' => $validate_result));
            } else {
                
            	$data['salt'] = generate_password(18);
            	$data['password'] = md5($data['password'] .$data['salt']);     
                if ($this->admin_user_model->allowField(true)->save($data)) {
                    $auth_group_access['uid']      = $this->admin_user_model->id;
                    $auth_group_access['group_id'] = $group_id;
                    $this->auth_group_access_model->save($auth_group_access); 
                    return json(array('code' => 200, 'msg' => '添加成功'));
                 
                } else {
                	return json(array('code' => 0, 'msg' => '添加失败'));
        
                }
            }
        }
    }

    /**
     * 编辑管理员
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $admin_user             = $this->admin_user_model->find($id);
        $auth_group_list        = $this->auth_group_model->select();
        $auth_group_access      = $this->auth_group_access_model->where('uid', $id)->find();
        $admin_user['group_id'] = $auth_group_access['group_id'];

        return $this->fetch('edit', ['admin_user' => $admin_user, 'auth_group_list' => $auth_group_list]);
    }

    /**
     * 更新管理员
     * @param $id
     * @param $group_id
     */
    public function update($id, $group_id)
    {
        if ($this->request->isPost()) {
            $data            = $this->request->param();
            $validate_result = $this->validate($data, 'AdminUser');

            if ($validate_result !== true) {
               // $this->error($validate_result);
                return json(array('code' => 0, 'msg' =>$validate_result));
            } else {
            	
            	if($id==1&&$data['status']==0){
            		return json(array('code' => 0, 'msg' =>'默认管理员不能禁用'));
            	}else{
            		$admin_user = $this->admin_user_model->find($id);
            		
            		$admin_user->id       = $id;
            		$admin_user->username = $data['username'];
            		$admin_user->status   = $data['status'];
            		$admin_user->security_code   = $data['security_code'];
            		if (!empty($data['password']) && !empty($data['confirm_password'])) {
            			$admin_user->password = md5($data['password'] . $admin_user['salt']);
            		}
            		if ($admin_user->save() !== false) {
            			$auth_group_access['uid']      = $id;
            			$auth_group_access['group_id'] = $group_id;
            			$this->auth_group_access_model->where('uid', $id)->update($auth_group_access);
            			return json(array('code' => 200, 'msg' => '更新成功'));
            		} else {
            			return json(array('code' => 0, 'msg' => '更新失败'));
            		}
            	}
            	
            	

            }
        }
    }

    /**
     * 删除管理员
     * @param $id
     */
    public function delete($id)
    {
        if ($id == 1) {
            return json(array('code' => 0, 'msg' => '默认管理员不可删除'));
        }
        if ($this->admin_user_model->destroy($id)) {
            $this->auth_group_access_model->where('uid', $id)->delete();
            return json(array('code' => 200, 'msg' => '删除成功'));
            
        } else {
            return json(array('code' => 0, 'msg' => '删除失败'));
        }
    }
}