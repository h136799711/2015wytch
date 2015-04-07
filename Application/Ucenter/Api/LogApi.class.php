<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Api;
use Common\Api\Api;
use Ucenter\Model\LogModel;
class LogApi extends Api{
	
	protected function _init(){
		$this->model = new LogModel();
	}
		
}
