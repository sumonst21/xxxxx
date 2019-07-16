<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Region extends Base
{
    function index(){
        return view('');
    }
    function cache(){
        $model = model('Region');
        $ret = $model->select();
        $ret = $ret->toArray();
        $ret = $this->list_to_tree($ret);
        $s=[];
        foreach ($ret as $k1 => $v1) {
            $s2=[];
            if(!isset($v1['_child'])){
                ///P($v1);
                //exit;                
            }else{
                foreach ($v1['_child'] as $k2 => $v2) {
                    $s3=[$v2['title']];
                    if(!isset($v2['_child'])){
                        //P($v2);
                        //exit;
                    }else{
                        foreach ($v2['_child'] as $k3 => $v3) {
                            $s3[]=$v3['title'];
                        }            
                    }
                    $s2[]=implode(',', $s3);
                }
            }
            $s[]=$v1['title'].'$'.implode('|', $s2);
        }
        $s = implode('#',$s);
        file_put_contents('./static/admin/js/PCASClass.js',$this->fetch('Base:pcas',['data'=>$s]));
        $this->success('已更新缓存');
    	//echo json_encode($ret);
    }
    /* 分类树 */
    function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
    {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}
