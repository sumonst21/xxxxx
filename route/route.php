<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


Route::pattern([
    'name'  =>  '\w+',
    'id'    =>  '\d+',
    'cid'    =>  '\d+',
]);
Route::domain('admin', 'admin');
Route::domain('www', 'home');
Route::domain('m', 'mobile');

Route::domain(['www','m',''], function () {
    // 动态注册域名的路由规则
    Route::get(':id','Article/read'); // 定义GET请求路由规则
    Route::get('Tag/:tag','Article/tag'); // 定义GET请求路由规则
    Route::get('Category/:cid','Article/category');
    
    Route::get('Video/:id','Video/view');
    Route::get('Video/Category/:cid','Video/category');
    Route::get('Video/tag/:tag','Video/tag'); 
    
    Route::get('/login','Member/login'); 
    Route::get('/reg','Member/reg'); 
    
});
