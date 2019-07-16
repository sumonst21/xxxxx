<?php
return [
    'charset' =>'GBK',
    'cate'=>[
        '都市激情'=>'24',
        '人妻交换'=>'25',
        '校园小说'=>'26',
        '家庭乱伦'=>'27',
        '武侠古典'=>'28',
        '另类小说'=>'29',
        '情色笑话'=>'30',
        '性爱技巧'=>'31',
        '自拍偷拍'=>'16',
        '亚洲色图'=>'17',
        '欧美色图'=>'18',
        '美腿丝袜'=>'19',
        '清纯唯美'=>'20',
        '乱伦熟女'=>'21',
        '卡通动漫'=>'22',
        '极品美女'=>'23',
    ],
    'host'=>'http://www.wo4567.com',
    'list'=>[
        'url'   =>  function($cid,$page){
            if($page && $page>1){
                return "/html/part/index{$cid}_{$page}.html";
            }else{
                return "/html/part/index{$cid}.html";
            }
        },
        'regex' =>  array(
            'title' => ['.textList li a','html','-span'],
            'url' => ['.textList li a','href'],
        )
    ],
    'view'=>[
        'url'   =>  function($url){
            return $url;
        },
        'regex'   => array(
            'title'=>['.mainArea p>font','text'],
            'body'=>array('div.mainArea table td','html','-script'),
        ),
        'range'=>'body'
    ]
];
?>