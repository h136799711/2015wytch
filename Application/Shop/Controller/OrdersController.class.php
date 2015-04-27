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

	private function getUserinfo(){
//		return array('id'=>1);
		return $this->userinfo;
	}
	
	private function formatOrderData($list){
		foreach($list as &$vo){
			$vo['createtime'] = date("Y-m-d H:i:s",$vo['createtime']);
			$vo['order_status'] = getOrderStatus($vo['order_status']);
			$vo['pay_status'] = getPayStatus($vo['pay_status']);
		}
		return $list;
	}
		
	/**
	 * 支付成功后js跳转到此链接
	 */
	public function paysuccess(){
		//跳转到订单中学
		$this->redirect(('Shop/Index/orders'));
//		$this->display();
	}
	
	/**
	 * 微信支付页面
	 */
	public function pay() {
		$id = I('get.id', 0);			
		addWeixinLog($id,"[订单总金额（单位：分）]");
		addWeixinLog(I('get.'),'pay get');
		if($id == 0){
			$this->error("参数错误！");
		}
		$result = apiCall("Admin/OrdersInfoView/getInfo", array(array('id' => $id)));
		if ($result['status']) {

			$order = $result['info'];
			addWeixinLog($order,'pay order info');
			$payConfig = C('WXPAY_CONFIG');
			$payConfig['jsapicallurl'] = $this->getCurrentURL();
			$items = unserialize($order['items']);
			$itemdesc = $items[0]['item'];
			$trade_no = $order['orderid'];
			$total_fee = $order['price']*100.0;
			addWeixinLog($order,"[订单总金额（单位：分）]");
//			addWeixinLog($total_fee,"[支付总金额（单位：分）]");
			if($total_fee <= 0){
				$this->error("支付金额不能小于0！");
			}
			$this -> setWxpayConfig($payConfig, $trade_no, $itemdesc, $total_fee);
			addWeixinLog($payConfig['notifyurl'],"[通知回调url]");
			$this -> assign("order",$order);
//			$this -> assign("url",$this->getCurrentURL());
			$this -> assign("items",$items);
			$this -> display();

		}else{
			$this->error("支付失败！");
		}

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
		addWeixinLog(I('post.'),'[order]save');
		$userinfo = $this->userinfo;
//		$userinfo = array('id'=>1);
		if (IS_POST && is_array($userinfo)) {
			addWeixinLog($userinfo,'[session]saveispost');
			$entity = array(
				'wxaccountid'=>$this->wxaccount['id'],
				'wxuser_id' => $userinfo['id'], 
				'price' => I('post.totalprice', 0), 
				'mobile' => I('post.mobile', ''), 
				'wxno' => I('post.wxno', ''), 
				'contactname' => I('post.contactname', ''), 
				'note' => I('post.note', ''), 
				'country' => I('post.country', ''), 
				'province' => I('post.p_name', ''), 
				'city' => I('post.c_name', ''), 
				'area' => I('post.a_name', ''), 
				'wxno' => I('post.wxno', ''), 
				'detailinfo' => I('post.address', ''), 
				'orderid' => $this -> getOrderID(), 
				'items' => $this -> getItems()
			 );
			$result = apiCall("Shop/Orders/addOrder", array($entity));
			
			if ($result['status']) {
				$id = $result['info'];	
				addWeixinLog($id,"insertOrderId = ");
				$address = array('wxuserid' => $userinfo['id'], 
				'country' => I('post.country', ''), 
				'province' => I('post.province', ''), 
				'city' => I('post.city', ''), 
				'detailinfo' => I('post.address', ''), 
				'area' => I('post.area', ''),
				'mobile' => I('post.mobile', ''), 
				'wxno' => I('post.wxno', ''), 
				'contactname' => I('post.contactname', ''), 
				);
				$result = apiCall("Shop/Address/addOrUpdate", array($address));
				if ($result['status']) {
//					dump($result);
//					$this -> success("操作成功！", U('Shop/Orders/pay') . "?id=$id&showwxpaytitle=1");
					$this -> success("操作成功！", C("SITE_URL") . "/index.php/Shop/Orders/pay/id/$id.shtml?showwxpaytitle=1");
				} else {
					LogRecord($result['info'], __FILE__ . __LINE__);
				}
			}else{
				$this -> error($resutl['info']);
			}
//			dump($address);
		} else {
			LogRecord("禁止访问！", __FILE__.__LINE__);
			$this -> error("禁止访问！");
		}

	}

	private function getItems() {
		$items = array( array('n'=>I('post.amount',0),'pic'=>I('post.pic',''),'item' => I('post.productname', ''), 'price' => I('post.price', 0)), );

		return serialize($items);
	}

	private function getOrderID() {
		return  date('YmdHis', time()). $this -> randInt().$this -> wxaccount['id'];
	}

	private function randInt() {
		srand(GUID());
		return rand(10000000, 99999999);
	}
	
	
	
		
	/**
	 * 
	 * @param config 微信支付配置
	 * @param trade_no 订单ID
	 * @param itemdesc 商品描述
	 * @param total_fee 总价格
	 */
	private function setWxpayConfig($config, $trade_no, $itemdesc, $total_fee, $prodcutid = 1) {
		try {
			//使用jsapi接口
			$jsApi = new \Common\Api\WxpayJsApi($config);

			//=========步骤1：网页授权获取用户openid============
			//通过code获得openid
			if (!isset($_GET['code'])) {
				//触发微信返回code码
				$url = $jsApi -> createOauthUrlForCode($config['jsapicallurl']);
				//				$url = $url.'?showwxpaytitle=1';
				Header("Location: $url");
			} else {
				//获取code码，以获取openid
				$code = $_GET['code'];
				$jsApi -> setCode($code);
				$result = $jsApi -> getOpenId();
			}
			$openid = "";
			if ($result['status']) {
				$openid = $result['info'];
			} else {
				$this -> error($result['info']);
			}
			//			dump($openid);
			//			dump($result);
			//			exit();
			//=========步骤2：使用统一支付接口，获取prepay_id============

			//使用统一支付接口
			$unifiedOrder = new \Common\Api\UnifiedOrderApi($config);

			//设置统一支付接口参数
			//设置必填参数
			//appid已填,商户无需重复填写
			//mch_id已填,商户无需重复填写
			//noncestr已填,商户无需重复填写
			//spbill_create_ip已填,商户无需重复填写
			//sign已填,商户无需重复填写
			$unifiedOrder -> setParameter("openid", "$openid");
			//商品描述
			$unifiedOrder -> setParameter("body", $itemdesc);
			//商品ID
			//$unifiedOrder -> setParameter("product_id", "$prodcutid");
			//商户订单号
			$unifiedOrder -> setParameter("out_trade_no", "$trade_no");
			//总金额
			$unifiedOrder -> setParameter("total_fee", "$total_fee");
			//通知地址
			$unifiedOrder -> setParameter("notify_url", $config['notifyurl']);
			$unifiedOrder -> setParameter("trade_type", "JSAPI");
			addWeixinLog($unifiedOrder,"[unifiedOrder]");
			//$unifiedOrder->setParameter("attach",'{"token":"'.'123'.'","orderid":"'.'456'.'"}');//附加数据
			//交易类型//商户订单号
			//非必填参数，商户可根据实际情况选填
			//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
			//$unifiedOrder->setParameter("device_info","XXXX");//设备号
			//$unifiedOrder->setParameter("attach","XXXX");//附加数据
			//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
			//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
			//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
			//$unifiedOrder->setParameter("openid","XXXX");//用户标识
			
			$prepay_id = $unifiedOrder -> getPrepayId();
			//=========步骤3：使用jsapi调起支付============
			$jsApi -> setPrepayId($prepay_id);

			$jsApiParameters = $jsApi -> getParameters();

			if (!empty($jsApiParameters -> return_msg)) {
				$this -> assign("error", $error);
//				$this -> error($jsApiParameters -> return_msg);
			}
			addWeixinLog($jsApiParameters,"设置微信支付配置！");
			//			dump($unifiedOrder);
			//			dump($jsApiParameters);
			//			exit();
			//			$returnUrl = U('Home/WxpayTest/return',array(''));

			$this -> assign("jsapiparams", $jsApiParameters);
			//      	$this->assign("params", json_decode($jsApiParameters));
			//	        $this->assign('returnUrl', $returnUrl);
		} catch(SDKRuntimeException $sdkexcep) {
			$error = $sdkexcep -> errorMessage();
			$this -> assign("error", $error);
//			$this -> error($error);
		}

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
