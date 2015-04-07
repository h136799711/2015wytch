<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Model;
use Think\Model;



class WxuserModel extends Model{
	
	//自动验证
	protected $_validate = array(
		array('nickname','require','昵称必须！'),
		array('avatar','require','头像必须！'),
		array('referrer','require','推荐人必须！'),
		array('openid','require','openid参数必须！'),
		array('wxaccount_id','require','公众号ID参数必须！'),
		
		array('sex', 'require','性别必须！'), 
		array('subscribe_time', 'require','关注时间必须！'), 
		
	);
	
	//自动完成
	protected $_auto = array(
		array('subscribed', 1, self::MODEL_INSERT), 
		array('money', 0, self::MODEL_INSERT), 
		array('updatetime', 'time', self::MODEL_BOTH,'function'), 
		array('createtime', NOW_TIME, self::MODEL_INSERT), 
		array('notes', '', self::MODEL_INSERT), 		
		array('score',0, self::MODEL_INSERT), 
		array('status', '1', self::MODEL_INSERT), 
	);
	
}
