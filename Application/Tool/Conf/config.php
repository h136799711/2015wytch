<?php

//客户端需要与这个一致

return array(
	//自定义配置
	"DEFAULT_THEME"=>"default",
	'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
	'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
	'VAR_LANGUAGE'     => 'l', // 默认语言切换变量
	//多语言
	'LANG_SWITCH_ON'=>true,
	'TMPL_PARSE_STRING'  =>array(
     	'__CDN__' => __ROOT__.'/Public/cdn', // 更改默认的/Public 替换规则
		'__JS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/js', // 增加新的JS类库路径替换规则
     	'__CSS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/css', // 增加新的JS类库路径替换规则
     	'__IMG__'     => __ROOT__.'/Public/'.MODULE_NAME.'/imgs', // 增加新的JS类库路径替换规则	
     
	),	
	'TASK_KEY'=>'hebidu',
    // 数据库配置
    'DB_TYPE'                   =>  'mysql',
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'wytch_',
    
	
);
