<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

class ShoppingCartController extends ShopController{
	
	private $cart;
	
	protected function _initialize(){
		parent::_initialize();
		
		$this->cart = session("shoppingcart");
		
	}
	
	public function add(){
		$id = I('p_id',0);
		
		if($id > 0){
			
			//商品信息，
			//p_id,main_img,name,ori_price ,price,icon_url,sku_id,
			
			
		}else{
			$this->success("操作成功");
		}
		
	}
	
	
	
	
}

