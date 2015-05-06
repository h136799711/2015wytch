<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 系统内跳转链接选择
 */
class LinkChooseController extends AdminController{
	
	public function index(){
		$wxaccount = getWxaccount();
		if(is_array($wxaccount)){
			$this->assign("token",$wxaccount['token']);
		}
		
		$this->display();
	}
	
	
	
	
}

