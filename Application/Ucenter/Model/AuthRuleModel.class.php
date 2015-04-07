<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Model;
use Think\Model;

/**
 * AuthRule 权限用户规则\节点表
 * title,status,rules1	id	mediumint(8)		UNSIGNED	否	无	AUTO_INCREMENT	
	2	module	varchar(20)	utf8_general_ci		否	无		
	3	type	tinyint(2)			否	1		
	4	name	char(80)	utf8_general_ci		
	5	title	char(20)	utf8_general_ci		
	6	status	tinyint(1)			否	1		
	7	condition	
 */
class AuthRuleModel extends Model{
	
	
	protected $tablePrefix = "common_";
	//自动验证
	protected $_validate = array(
		array('title', 'require', '标题必须'),
		array('module', 'require', '所属模块必须'),
		array('type', 'require', '类型必须'),
	);
	
	//自动完成
	protected $_auto = array(
		array('status', '1', self::MODEL_INSERT), 
	);
}
