<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace WeixinShop\Controller;

class ProductController extends WeixinShopController{
	
	/**
	 * 选择类目
	 */
	public function cate(){
		if(IS_POST){
			//保存
		}else{
			$this->display();
		}
	}
	
	/**
	 * 添加商品
	 */
	public function add(){
		
	}
	
}
