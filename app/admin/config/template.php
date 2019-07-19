<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
return [
    'tpl_replace_string'       => [
	    '__PUBLIC__'=>'/static/admin',
	    '__STATIC__' => '/static',
        '__APP__' => __APP__,
        '__SELF__' => $_SERVER['REDIRECT_URL'],
        '__ACTION__' => $_SERVER['REDIRECT_URL'],
    ],
    'layout_on'     =>  true,
    'layout_name'   =>  'layout',
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',
    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end'   => '}',
    // 预先加载的标签库
    'taglib_pre_load'     =>    'app\common\taglib\Data,app\common\taglib\Sd,app\common\taglib\Html',
];