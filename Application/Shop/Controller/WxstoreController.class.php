<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

class WxstoreController extends ShopController{
	
	/**
	 * TODO: 店铺查看
	 */
	public function view(){
		$this->error("TODO:店铺查看");
//		$this->display();
	}
	
	/**
	 * TODO: 查看所有的宝贝分类
	 */
	public function allCategory(){
		$this->error("TODO:查看所有的宝贝分类");
//		$this->display();
	}
	
	/**
	 * 搜索店铺
	 */
	public function search(){
//		
//		$q = I('get.q');
//		
//		$map = array();
//		$map['name'] = array('like','%'.$q.'%');
//		
//		$result = apiCall("Shop/Wxstore/query", array($map));
//		
		$this->display();
		
	}
}

