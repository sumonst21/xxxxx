<?php
return[
'__pattern__'=>['name'=>'\w+',],
''=>['index/index/index',['ext'=>'html']],
'soso'=>['index/index/search',['ext'=>'html']],
'artadd'=>['index/articles/add',['ext'=>'html']],
'choice'=>['index/articles/choice',['ext'=>'html']],

'[artedit]'=>[':id'=>['index/articles/edit',['id'=>'\d+']]],
'[html]'=>[':id'=>['index/index/html',['id'=>'\d+']]],
'[soft]'=>[':id'=>['index/index/soft',['id'=>'\d+']]],
'[article]'=>[':id'=>['index/index/article',['id'=>'\d+']]],
'[lists]'=>[':cate_alias'=>['index/articles/lists',['cate_alias'=>'\d+']]],
'all'=>['index/articles/lists',['ext'=>'html']],
'forums'=>['bbs/index/index',['ext'=>'html']],
'search'=>['bbs/index/search',['ext'=>'html']],
'fadd'=>['bbs/forum/add',['ext'=>'html']],
'plugins'=>['bbs/plugin/index',['ext'=>'html']],
'applydeveloper'=>['bbs/plugin/applydeveloper',['ext'=>'html']],
'[plinfo]'=>[':id'=>['bbs/plugin/view',['id'=>'\d+']]],
'[fedit]'=>[':id'=>['bbs/forum/edit',['id'=>'\d+']]],
'[thread]'=>[':id'=>['bbs/index/thread',['id'=>'\d+']]],
'[cate]'=>[':cate_alias'=>['bbs/index/cate',['cate_alias'=>'\d+']]],

'[home]'=>[':id'=>['user/index/home',['id'=>'\d+']]],
'logining'=>['user/login/index',['ext'=>'html']],
'reging'=>['user/login/reg',['ext'=>'html']],
'forget'=>['user/login/forget',['ext'=>'html']],
'userset'=>['user/index/set',['ext'=>'html']],
'usercenter'=>['user/index/index',['ext'=>'html']],
'messages'=>['user/index/message',['ext'=>'html']],
'comments'=>['user/index/comment',['ext'=>'html']],
'myarticle'=>['user/index/article',['ext'=>'html']],
'mytopic'=>['user/index/topic',['ext'=>'html']],

// '[adminx]'=>[':login'=>['admin/login/index',['login'=>'\d+']]],
];
