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
    Route::get(':id','Article/read');
    Route::get('Tag/:tag','Article/tag');
    Route::get('Category/:cid','Article/category');

    Route::get('Article/:id','Article/view');
    Route::get('Article/Category/:cid','Article/category');
    Route::get('Article/tag/:tag','Article/tag'); 

    Route::get('Video/:id','Video/view');
    Route::get('Video/Category/:cid','Video/category');
    Route::get('Video/tag/:tag','Video/tag'); 
    
    Route::rule('login','User/login'); 
    Route::rule('reg','User/reg'); 
    
});
