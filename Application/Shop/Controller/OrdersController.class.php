<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;
use Think\Controller;
class OrdersController extends ShopController {

	protected function _initialize() {
		parent::_initialize();
	}
	
	/**
	 * 订单确认
	 * 运费的计算规则，
	 * 1. 同一家的商品，购商品中运费价格总和的作为最终运费
	 * TODO: 暂不支持运费模板的运费计算
	 */
	public function confirm(){
		$fromsession = I('get.fromsession',0);
		$p_id_arr = I('post.p_id',array());
		$sku_id_arr = I('post.sku_id',array());
		$price_arr = I('post.price',array());
		$count_arr = I('post.count',array());
		
		//针对多规格商品的立即购买操作
		$hebidu_skuchecked = I('post.hebidu_skuchecked','');
		if(!empty($hebidu_skuchecked)){
			array_push($sku_id_arr,$hebidu_skuchecked);
			$count_arr[0] = I('post.sku_count',1);
		}
		
		if(intval($fromsession) == 1){
			//从session中取
			$list = session("confirm_order_info");
		}else{
			
			if(count($p_id_arr) == 0){
				LogRecord("参数错误", __FILE__.__LINE__);
				$this->error("参数错误！");
			}
			
			//获取商品信息
			$map = array();
			$map['id'] = array('in',$p_id_arr);
			$order = " id desc ";
			$result = apiCall("Shop/Wxproduct/queryNoPaging", array($map,$order));
			
			if(!$result['status']){
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);
			}
			$product_list = $result['info'];
			//获取商品SKU信息
			unset($map['id']);
			array_push($sku_id_arr,-1);
			$map['sku_id'] = array('in',$sku_id_arr);
			$order = " product_id desc ";
			$result = apiCall("Shop/WxproductSku/queryNoPaging", array($map,$order));
			if(!$result['status']){
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);
			}
			//商品数量与商品进行关联
			$tmp_count = array();
			for($i = 0 ; $i < count($count_arr) ; $i++){
				$tmp_count[$p_id_arr[$i]] = $count_arr[$i];		
			}
			
			$product_sku_list = $result['info'];
			$tmp_arr = array();
			$store_ids = array();
			foreach($product_sku_list as $vo){
				$tmp_arr[$vo['product_id']] = $vo;
			}
			
			//商品SKU、运费计算与商品进行关联
			$all_price = 0.0;
			$all_express = 0.0;
			$tmp_store = array();
			foreach($product_list as &$vo){
	//			if(isset($tmp_arr[$vo['id']])){
	//				$vo['_sku_obj'] = $tmp_arr[$vo['id']];	
	//			}else{
	//				$vo['_sku_obj'] = array();
	//			}
				$entity = array(
					'has_sku'=>$vo['has_sku'],//标志是否使用SKU信息
					'img'=>$vo['main_img'],
					'price'=>$vo['price'],
					'ori_price'=>$vo['ori_price'],
					'name'=>$vo['name'],
					'sku_id'=>'',
					'sku_desc'=>$tmp_arr[$vo['id']],
					'count'=>$tmp_count[$vo['id']]//购买商品数量
				);
				
				if(intval($vo['has_sku']) == 1){
					//有规格的情况下
					$entity['price'] = $tmp_arr[$vo['id']]['price'];
					$entity['sku_id'] = $tmp_arr[$vo['id']]['sku_id'];
					$entity['ori_price'] = $tmp_arr[$vo['id']]['ori_price'];
					if(!empty($tmp_arr[$vo['id']]['icon_url'])){
						$entity['img'] = $tmp_arr[$vo['id']]['icon_url'];
					}
				}
				
				if(!isset($tmp_store[$vo['storeid']])){
					$tmp_store[$vo['storeid']] = array('products'=>array(),'post_price'=>0.0,'total_price'=>0.0);
				}
				
				$all_price += ($entity['count']*$entity['price']/100.0);
				//每个店铺的总价
				$tmp_store[$vo['storeid']]['total_price'] += ($entity['count']*$entity['price']/100.0);
				//取得该商品的运费
				$express_price = $this->getExpressPrice($vo);
				$express_price = $express_price / 100.0;//转化为元
				//取得最大的运费
//				if($tmp_store[$vo['storeid']]['post_price'] < $express_price){
				$entity['post_price'] = $express_price;
				$tmp_store[$vo['storeid']]['post_price'] += $express_price;
				$all_express += $express_price;
//				}
				
				array_push($tmp_store[$vo['storeid']]['products'],$entity);
				array_push($store_ids,$vo['storeid']);
			}
			
			unset($map['sku_id']);
			$map['id'] = array('in',$store_ids);
			$order = " id asc ";
			$result = apiCall("Shop/Wxstore/queryNoPaging", array($map,$order));
			if(!$result['status']){
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);
			}
			$stores = $result['info'];
			
			foreach($stores as &$vo){
				if(isset($tmp_store[$vo['id']])){
					$tmp_store[$vo['id']]['store'] = $vo;
				}
			}
			
			
			$list = array(
				'list'=>$tmp_store,
				'all_price'=> $all_price,
				'all_express'=> $all_express,
			);
			
			session("confirm_order_info",$list);
		}

		//获取默认收货地址		
		$result = apiCall("Shop/Address/getInfo", array(array('wxuserid'=>$this->userinfo['id'],'default'=>1)));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__ . __LINE__);
		}
		
		$default_address = $result['info'];//默认收货地址
		$province_one = apiCall("Tool/Province/getInfo",array(array("provinceID"=>$default_address['province'])));
		$city_one = apiCall("Tool/City/getInfo",array(array("cityID"=>$default_address['city'])));
		$area_one = apiCall("Tool/Area/getInfo",array(array("areaID"=>$default_address['area'])));
		
		if(is_array($province_one['info'])){
			$default_address['province_name'] = $province_one['info']['province'];
		}
		if(is_array($city_one['info'])){
			$default_address['city_name'] = $city_one['info']['city'];
		}
		if(is_array($area_one['info'])){
			$default_address['area_name'] = $area_one['info']['area'];
		}
		
		
		$this->assign("default_address",$default_address);
		$this->assign("list",$list);
		$this->display();
		
	}
	
	public function save() {
		//收货地址ID
		$address_id = I('post.address_id',0);
		$province_name = I('post.province_name','');
		$city_name = I('post.city_name','');
		$area_name= I('post.area_name','');
		$notes = I('post.notes',array());
		$userinfo = $this->userinfo;
		if (IS_POST && is_array($userinfo)) {
			addWeixinLog($userinfo,'[session]saveispost');
			//购买的商品的列表
			$buyProductList =  session("confirm_order_info");
			addWeixinLog($buyProductList,'购买商品的列表');
			
			$map = array('id'=>$address_id);
			$result = apiCall("Shop/Address/getInfo", array($map));
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			if(is_null($result['info'])){
				LogRecord("收货地址信息获取失败", __FILE__.__LINE__);
				//如果是空的
				$this->error("收货地址信息获取失败！");
			}
			$address_info = $result['info'];
			//总价，不含运费
			$all_price = $buyProductList['all_price'];
			//运费
			$all_express = $buyProductList['all_express'];
			
			//订单对象
			$entity = array(
				'wxaccountid'=>$this->wxaccount['id'],
				'storeid' => 0,
				'wxuser_id' => $userinfo['id'], 
				'price' => 0, 
				'mobile' => $address_info['mobile'], 
				'wxno' => $address_info['wxno'], 
				'contactname' => $address_info['contactname'], 
				'note' => '', 
				'country' => $address_info['country'], 
				'province' => $province_name, 
				'city' => $city_name, 
				'area' => $area_name, 
				'detailinfo' => $address_info['detailinfo'], 
				'orderid' => '', 
				'items' => '',
			);
			$i = 0;
			$ids = '';
			$orderid = $this -> getOrderID();
			//分店铺保存订单，每个店铺一张订单
			foreach($buyProductList['list'] as $key=>$vo){
				//店铺ID
				$entity['storeid'] = $key;
				$entity['orderid'] = $orderid.'_'.$key;
				$entity['items'] = json_encode($vo,JSON_UNESCAPED_UNICODE);
				//
				$price  = 0.0;
				foreach($vo['products'] as $item){
					$price += $item['price'];
				}
				
				$entity['price'] = $price;
				if($i < count($notes)){
					$entity['note'] = $notes[$i];
					
				}
				
				$i++;
				$result = apiCall("Shop/Orders/addOrder", array($entity));
				if(!$result['status']){
					LogRecord($result['info'], __FILE__.__LINE__);
					$this->error($result['info']);
				}
				$ids .= $result['info'].'-';
			}
			
			$this->success("订单保存成功，前往支付！",U("Shop/OnlinePay/wxpay",array('id'=>$ids)));
			
		} else {
			LogRecord("禁止访问！", __FILE__.__LINE__);
			$this -> error("禁止访问！");
		}

	}
	
	
	
	private function getOrderID() {
		return  date('YmdHis', time()). $this -> randInt();
	}
	
	private function randInt() {
		srand(GUID());
		return rand(10000000, 99999999);
	}
	
	
	
		
	/**
	 * 获取商品的运费
	 */
	private function getExpressPrice($product){
		
		if(intval($product['attrext_ispostfree']) == 1){
			return 0;
		}
		
		if(intval($product['delivery_type']) == 0){
			//快递，EMS，
			$express = json_decode($product['express']);
			
			if(count($express) > 0){
				return ($express[0]->price);
			}else{
				return 0;
			}
			
		}elseif(intval($product['delivery_type']) == 1){
			//使用运费模板
		}
		
		return 0;
	}

}
