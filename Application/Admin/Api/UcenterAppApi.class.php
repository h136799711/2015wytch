<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Api;
use Common\Api\Api;
use Admin\Model\UcenterAppModel;


class UcenterAppApi extends  Api{
	
	protected function _init(){
		$this->model = new UcenterAppModel();
	}
	
	
	
}