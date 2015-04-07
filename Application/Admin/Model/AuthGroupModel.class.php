<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * AuthGroup 权限用户组
 * title,status,rules
 */
class AuthGroupModel extends Model{
	
	protected $tablePrefix = "common_";
	//自动验证
	protected $_validate = array(
		array('title', 'require', '{%MV_AUTHGROUP_TITLE}'),
		array('title','', '已经存在相同的名称',self::MUST_VALIDATE,"unique"),
		array('rules', 200 , '长度不能超过200个字符',self::VALUE_VALIDATE,'length')
	);
	
	//自动完成
	protected $_auto = array(
		array('status', '1', self::MODEL_INSERT), 
	);
}
