<?php
return [
    'charset' =>'UTF-8',
    'cate'=>[
        '校园小说'=>"/AAbook/AAAtb/xiaoyuan",
        '黄色小说'=>"/AAbook/AAAtb/huangse",
        '性教小说'=>"/AAbook/AAAtb/xingai",
        '强暴小说'=>"/AAbook/AAAtb/qiangjian",
        '淫妻小说'=>"/AAbook/AAAtb/yinqi",
        '乱伦文学'=>"/AAbook/AAAtb/luanlunx",
        '长篇小说'=>"/AAbook/AAAtb/changpian",
        '武侠小说'=>"/AAbook/AAAtb/wuxia",
        '另类小说'=>"/AAbook/AAAtb/lingleix",
        '亚洲情色'=>"/AAtupian/AAAtb/asia",
        '欧美色图'=>"/AAtupian/AAAtb/oumei",
        '网友自拍'=>"/AAtupian/AAAtb/zipai",
        '清纯美眉'=>"/AAtupian/AAAtb/meimei",
        '美腿色图'=>"/AAtupian/AAAtb/meitui",
        '明星色图'=>"/AAtupian/AAAtb/mingxing",
        '情色动漫'=>"/AAtupian/AAAtb/cartoon",
        '乱伦色图'=>"/AAtupian/AAAtb/luanlun",
        '另类色图'=>"/AAtupian/AAAtb/linglei",
    ],
    'host'=>'http://www.x3f9.com',
    'list'=>[
        'url'   =>  function($cid,$page){
            if($page && $page>1){
                return "{$cid}/index-{$page}.html";
            }else{
                return "{$cid}/index.html";
            }
        },
        'regex' =>  array(
            'title' => array('.classList ul a','text'),
            'url' => array('.classList ul a','href'),
        )
    ],
    'view'=>[
        'url'   =>  function($url){
            return $url;
        },
        'regex'   => array(
            'title'=>array('h1.content','text'),
            'body'=>array('div.content','html','-script -.jogger'),
        ),
        'range'=>'body'
    ]
];
?>