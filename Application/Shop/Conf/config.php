<?php
return array(
	//'配置项'=>'配置值',
	'TMPL_PARSE_STRING'  =>array(
     	'__CDN__' => __ROOT__.'/Public/cdn', // 更改默认的/Public 替换规则
		'__JS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/js', // 增加新的JS类库路径替换规则
     	'__CSS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/css', // 增加新的JS类库路径替换规则
     	'__IMG__'     => __ROOT__.'/Public/'.MODULE_NAME.'/imgs', // 增加新的JS类库路径替换规则	
     
	),	
	'DEFAULT_THEME'=>'default',
	'SESSION_PREFIX'=>'Shop_',
    // 数据库配置
    'DB_TYPE'                   =>  'mysql',
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'wytch_',
    
     //token，TODO: 一套系统对应一个公众号的商城
    'SHOP_TOKEN'=>'eotprkjn1426473619',
    
	'SHOW_PAGE_TRACE'=>false,
	
);