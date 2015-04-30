<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

class UserController extends ShopController{
	
	public function index(){
		
		$this->display();
	}
	
	/**
	 * 用户个人中心
	 */	
	public function info(){
		if(IS_GET){
			$this->display();
		}
	}
	
	/**
	 * 个人订单
	 */
	public function order(){
		
		if(IS_GET){
			$this->display();
		}
		
	}
}

