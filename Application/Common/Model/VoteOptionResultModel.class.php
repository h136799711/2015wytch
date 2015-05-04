<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Common\Model;
use Think\Model;

class VoteOptionResultModel extends Model{
	protected $_auto = array(		
		array('vote_time', NOW_TIME, self::MODEL_INSERT), 
		array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
	);
}
