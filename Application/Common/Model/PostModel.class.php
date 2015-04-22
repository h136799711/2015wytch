<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 文章发布
 */
class PostModel extends Model{
	
	protected $_auto = array(
		array("post_date",NOW_TIME,self::MODEL_INSERT),
		array("post_modified","time",self::MODEL_BOTH,"function"),
	);
}
