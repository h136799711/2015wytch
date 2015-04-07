<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Tool\Controller;
use Think\Controller;

class WxpayController extends Controller{
	public function problem(){
		$post = I('post.');
		addWeixinLog($post,'[微信告警接口]');
		LogRecord(serialize($post), '[微信告警接口]');
		echo "";
	}
}
