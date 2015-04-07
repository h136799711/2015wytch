<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Controller;

class CheckLoginController extends BaseController{
	//初始化
	protected function _initialize(){
		parent::_initialize();
		
		if (session('?uid') && is_login() > 0) {
			if(!defined("UID")){
	        	define('UID',session('uid'));
			}
		} else {
			$this->error(L('ERR_SESSION_TIMEOUT'),U('Public/logout'),3);
			
		}
	}
}
