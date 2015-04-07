<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

class GroupAccessModel extends Model{
	protected $_auto = array(
		'alloweddistribution'=>0,
		'allowedcomment'=>0,		
	);
	
	protected $_validate = array(
	);
	
	
}
