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
 * AuthGroupAccessModel 用户组与用户对应表
 */
class AuthGroupAccessModel extends Model{
	
	protected $tablePrefix = "common_";
	//自动验证
	protected $_validate = array(
	);
	
	//自动完成
	protected $_auto = array(
	);
}
