<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Common\Model;
use Think\Model;
class WeixinLogModel extends Model{
	
	//自动验证
	protected $_validate = array(
		
	);
	
	//自动完成
	protected $_auto = array(
		array('ctime', NOW_TIME, self::MODEL_INSERT), 
	);
	
}