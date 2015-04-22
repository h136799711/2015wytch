<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

class WxproductModel extends Model{
	
	/**
	 * 上架
	 */
	const STATUS_ONSHELF = 1;
	
	/**
	 * 下架
	 */
	const STATUS_OFFSHELF = 0; 
	
	
	protected $_auto = array(
		array('updatetime', 'time', self::MODEL_BOTH,'function'), 
		array('createtime', NOW_TIME, self::MODEL_INSERT), 
		array('status', 1, self::MODEL_INSERT), 
		
	);
	
	
}

