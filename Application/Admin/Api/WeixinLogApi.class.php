<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use Common\Api\Api;

use Common\Model\WeixinLogModel;

class WeixinLogApi extends Api{
	
	protected function _init(){
		$this->model = new WeixinLogModel();
	}
	
	
}
