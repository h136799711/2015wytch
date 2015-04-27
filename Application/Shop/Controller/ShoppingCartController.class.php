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
		if(!session('?shoppingcart')){
			session("shoppingcart",array());
		}
		$this->cart = session("shoppingcart");
	}
	
	
	/**
	 * 购物车查看页面
	 */
	public function index(){
		session("confirm_order_info",null);
		$store = $this->getStoreInfo();
		
		$this->assign("stores",$store);
		$this->assign("cart",$this->cart);
		$this->display();
	}
	
	/**
	 * 从购物车中移除一项物品
	 */
	public function delItem(){
		$s_id = I('get.s_id',0);//店铺ID，
		$p_id = I('get.p_id',0);//商品ID，
//		urldecode($str)
		$sku_id = I('get.sku_id','',"urldecode");//商品ID，
		if($s_id === 0 || $p_id === 0 ){
			$this->error("参数错误！");
		}
		if($this->removeFromCart($s_id, $p_id,$sku_id)){
			$this->success("删除成功！");
		}else{
			$this->error("操作失败！");
		}
		
	}
	
	/**
	 * 加入商品到购物车之中
	 */
	public function addItem(){
//		sleep(5);
		$id = I('p_id',0);
		$count = I('sku_count',1,'intval');//默认一件
		$sku_id = I('hebidu_skuchecked','');
		$sku_desc = I('sku_desc','');//SKU选择的信息描述
		if($count <= 0){
			$this->error("商品数量不对！");
		}
		
		if($id > 0){
			
			$result = apiCall("Admin/Wxproduct/getInfo", array(array('id'=>$id)));
			
			if(!$result['status']){
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);
			}
			if(is_null($result['info'])){
				$this->error("商品信息获取失败！");
			}
			$product = $result['info'];
			
//			dump($sku_id);
			$result = apiCall("Admin/WxproductSku/getInfo", array(array('sku_id'=>$sku_id)));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$sku = $result['info'];
			if($product['has_sku'] == 0){
				
				$this->addToCart($product,false,$count);
				
			}elseif($product['has_sku'] == 1 && is_array($sku)){
				
				if(is_null($sku)){
					$this->error("商品规格信息获取失败！");
				}
				
				$sku['sku_desc'] = $sku_desc;
				//多规格
				$this->addToCart($product,$sku,$count);
			}else{
				$this->error("操作失败！");
			}
			
			$this->success("成功添加到购物车！");
			
		}else{
			$this->error("参数错误");
		}
		
	}
	
	/**
	 * 商品id，商品名称，商品图片（默认主图），商品数量count，是否多规格，商品价格，商品原价，
	 * 运费,
	 */
	private function addToCart($product,$sku,$count){
		
		$item = array(
			'p_id'=>$product['id'],
			'icon_url'=>$product['main_img'],//商品图片
			'count'=>$count, // 商品数量
			'name'=>$product['name'],
			'has_sku'=>$product['has_sku'],
			'sku_desc'=>'',
			'sku_id'=>'',
			'storeid'=>$product['storeid'],
			'express'=>0,
		);
//		if($product['delivery_type'] == 0){
//			$express = json_decode($product['express']);
//			if(is_array($express) && count($express) > 0){
//				//TODO: 暂时只默认第一个快递价格，用户不能选择快递
//				$item['express'] = $express[0]－>price;
//			}
//			
//		}elseif($product['delivery_type'] == 1){
//			//TODO:使用运费模板的情况下
//		}
		if($sku === false){
			//统一规格下
			$item['price'] = $product['price'];
			$item['ori_price'] = $product['ori_price'];
		}else{
			$item['sku_id'] = $sku['sku_id'];
			$item['price'] = $sku['price'];
			$item['ori_price'] = $sku['ori_price'];
			$item['sku_desc'] = $sku['sku_desc'];
			if(!empty($sku['icon_url'])){
				$item['icon_url'] = $sku['icon_url'];//商品图片
			}
		}
		
		
		$exsit = false;
		//检测购物车是否有同样的商品
		foreach($this->cart[$item['storeid']] as &$vo){				
			
			if($vo['p_id'] == $item['p_id']){
//				dump($vo);
				if((intval($vo['has_sku']) == 1 && $vo['sku_id'] == $item['sku_id'])){
					$vo['count'] = $vo['count'] + $count;
					$exsit = true;
					break;
				}
				elseif($vo['has_sku'] == 0){
					$vo['count'] = $vo['count'] + $count;
					$exsit = true;
					break;
				}
			}
		}
		
//		dump($exsit);
//		dump($this->cart[$item['storeid']]);
//		exit();
		if(!$exsit){	
			if(!isset($this->cart[$product['storeid']])){
				$this->cart[$product['storeid']] = array($item);
			}else{
				array_push($this->cart[$product['storeid']],$item);
			}
		}
		session("shoppingcart",$this->cart);
	}
	
	/**
	 * 获取商品的店铺信息
	 * logo,店铺名称，店铺ID，店铺微信号
	 */
	private function getStoreInfo(){
		$store_ids = '';
		foreach($this->cart as $key =>$vo){
			$store_ids .= ($key.",");
		}
		$map['id'] = array('in',$store_ids);
		$result = apiCall("Shop/Wxstore/queryNoPaging", array($map));
		
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
			$this->error($result['info']);
		}
		$store_info = array();
		if(is_array($result['info'])){
			
			foreach($result['info'] as $vo){				
					$store_info[$vo['id']] = $vo;
			}
		}
		return $store_info;
	}
	
	/**
	 * 移除商品
	 */
	private function removeFromCart($s_id,$p_id,$sku_id){
		
		if(!isset($this->cart[$s_id])){
			return false;
		}

		$count = count($this->cart[$s_id]);
		$removeIndex = -1;
		for($i = 0 ;$i < $count;$i ++){
			$vo = $this->cart[$s_id][$i];
			if($vo['p_id'] == $p_id){
				if(intval($vo['has_sku']) == 1 && $vo['sku_id'] == $sku_id){
					$removeIndex = $i;
					break;
				}elseif(intval($vo['has_sku']) == 0){
					$removeIndex = $i;
					break;
				}
			}			
		}
//		dump($sku_id);
//		dump($removeIndex);
		if($removeIndex == -1){
			return false;
		}
//		exit();
		if($count == 1){
			//只有一件商品时
			unset($this->cart[$s_id]);
		}elseif($count > 1){
			array_splice($this->cart[$s_id],$removeIndex,1);
		}
		session("shoppingcart",$this->cart);
		
		return true;
	}

}

