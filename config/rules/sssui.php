<?php
return [
    'charset' =>'UTF-8',
    'cate'=>[
        "CSS"=>"/Category/168.html",
        "设计漫谈"=>"/Category/167.html",
        "花花世界"=>"/Category/164.html",
        "MySql"=>"/Category/163.html",
        "JS"=>"/Category/160.html",
        "PHP"=>"/Category/159.html",
        "码农的苦逼生活"=>"/Category/3.html",
        "服务器"=>"/Category/1.html"
    ],
    'host'=>'http://www.sssui.com',
    'list'=>[
        'url'   =>  function($cid,$page){
            if($page && $page>1){
                return $cid.'?page='.$page;
            }else{
                return $cid;
            }
        },
        'regex' =>  array(
            'title' => array('h2.title a','text'),
            'url' => array('h2.title a','href'),
        )
    ],
    'view'=>[
        'url'   =>  function($url){
            return $url;
        },
        'regex'   => array(
            'title'=>array('.read .title','text'),
            'body'=>array('.read .content','html','-script'),
        ),
        'range'=>'body'
    ]
];
?>