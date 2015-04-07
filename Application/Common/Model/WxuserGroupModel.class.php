<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Common\Model;
use Think\Model;

class WxuserGroupModel extends Model{
	
	
	protected $_validate = array(
		array('name','require','用户组名称必须！',self::EXISTS_VALIDATE ),		
	    array('name','','用户组名称必须唯一！',0,'unique',self::EXISTS_VALIDATE), // 在新增的时候验证name字段是否唯一
	);
	
	
}
