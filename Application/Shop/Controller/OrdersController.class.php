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
	 * 获取订单
	 * @param post.type 0=全部,1=待付款,2=待发货,3=待收货,4＝待评论
	 */
	public function orderlist() {
		$type = I('post.type', 0, 'intval');

		//是否获取物流信息
		$shouldGetExpressInfo = false;
		$map = array();
		if ($type == 1) {
			//待付款
			$map['pay_status'] = \Common\Model\OrdersModel::ORDER_TOBE_PAID;
		} else {
			//货到付款，在线已支付
			$map['pay_status'] = array('in', array(\Common\Model\OrdersModel::ORDER_PAID, \Common\Model\OrdersModel::ORDER_CASH_ON_DELIVERY));

		}

		if ($type == 2) {
			//1. 已支付、货到付款
			//2. 待发货
			//
			$map['order_status'] = \Common\Model\OrdersModel::ORDER_TOBE_SHIPPED;

		} elseif ($type == 3) {
			//1. 已支付、货到付款
			//2. 已发货
			$map['order_status'] = \Common\Model\OrdersModel::ORDER_SHIPPED;
			$shouldGetExpressInfo = true;
		} elseif ($type == 4) {
			//1. 已支付、货到付款
			//2. 已收货
			//3. 待评论
			$map['order_status'] = \Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS;
			$map['comment_status'] = \Common\Model\OrdersModel::ORDER_TOBE_EVALUATE;
			$shouldGetExpressInfo = true;

		}

		$map['wxuser_id'] = $this -> userinfo['id'];
		//TODO: 订单假删除时不查询
		$map['status'] = 1;
		$orders = " createtime desc ";

		$result = apiCall("Shop/Orders/query", array($map, $page, $orders));

		ifFailedLogRecord($result, __FILE__ . __LINE__);

		//1. 订单信息
		$order_list = $result['info']['list'];
		$store_ids = array();
		$order_ids = array();
		$result_list = array();
		
		$store_key = array();
		
		foreach ($order_list as $vo) {
			$entity = array(
				'orderid' => $vo['id'], 
				'price' => $vo['price'], //订单总价
				'storeid' => $vo['storeid'], 
				'order_status'=>$vo['order_status'],
				'pay_status'=>$vo['pay_status'],
				'_items' => array(), //商品列表
				'_store' => array(), //店铺信息
			);
			
			$result_list[$vo['id']] = $entity;
			if(!array_key_exists($vo['storeid'], $store_key)){
				array_push($store_ids, $vo['storeid']);
				$store_key[$vo['storeid']] = $vo['storeid'];
			}
			array_push($order_ids, $vo['id']);
		}
		

		if (count($store_ids) > 0) {
			$mapStore = array();
			$mapStore['id'] = array('in', $store_ids);
			//2. 获取店铺信息
			$result = apiCall("Shop/Wxstore/queryNoPaging", array($mapStore));
			ifFailedLogRecord($result, __FILE__ . __LINE__);
			foreach ($result_list as &$vo_obj){
				foreach ($result['info'] as $vo) {
					if ($vo['id'] == $vo_obj['storeid']) {
						$vo_obj['_store'] = $vo;
						break;
					}
				}
			}

		}
		//3. 获取订单商品信息

		if (count($store_ids) > 0) {
			$mapStore = array();
			$mapStore['orders_id'] = array('in', $order_ids);
			$result = apiCall("Shop/OrdersItem/queryNoPaging", array($mapOrder));

			ifFailedLogRecord($result, __FILE__ . __LINE__);

			foreach ($result['info'] as $vo) {
				array_push($result_list[$vo['orders_id']]['_items'], $vo);
			}

		}
		if(IS_POST){
			$this->success($result_list);
		}else{
			$this->error("禁止访问！");
		}

	}

	/**
	 * 订单确认
	 * 运费的计算规则，
	 * 1. 同一家的商品，购商品中运费价格总和的作为最终运费
	 * TODO: 暂不支持运费模板的运费计算
	 */
	public function confirm() {
		$fromsession = I('get.fromsession', 0);
		$p_id_arr = I('post.p_id', array());
		$sku_id_arr = I('post.sku_id', array());
		$price_arr = I('post.price', array());
		$count_arr = I('post.count', array());

		//针对多规格商品的立即购买操作
		$hebidu_skuchecked = I('post.hebidu_skuchecked', '');
		if (!empty($hebidu_skuchecked)) {
			array_push($sku_id_arr, $hebidu_skuchecked);
			$count_arr[0] = I('post.sku_count', 1);
		}

		if (intval($fromsession) == 1) {
			if (session("?confirm_order_info")) {
				$this -> redirect('Shop/Index/index');
			}
			//从session中取
			$list = session("confirm_order_info");
			if (is_null($list)) {
				$this -> redirect('Shop/Index/index');
			}
		} else {

			if (count($p_id_arr) == 0) {
				LogRecord("参数错误", __FILE__ . __LINE__);
				$this -> error("参数错误！");
			}

			//获取商品信息

			$product_list = $this -> getProductList($p_id_arr);
			//获取商品SKU信息
			unset($map['id']);
			array_push($sku_id_arr, -1);
			$map['sku_id'] = array('in', $sku_id_arr);
			$order = " product_id desc ";
			$result = apiCall("Shop/WxproductSku/queryNoPaging", array($map, $order));
			if (!$result['status']) {
				LogRecord($result['info'], __FILE__ . __LINE__);
				$this -> error($result['info']);
			}
			//商品数量与商品进行关联
			$tmp_count = array();
			for ($i = 0; $i < count($count_arr); $i++) {
				$tmp_count[$p_id_arr[$i]] = $count_arr[$i];
			}

			$product_sku_list = $result['info'];
			$tmp_arr = array();
			$store_ids = array();
			foreach ($product_sku_list as $vo) {
				$tmp_arr[$vo['product_id']] = $vo;
			}

			//商品SKU、运费计算与商品进行关联
			$all_price = 0.0;
			$all_express = 0.0;
			$tmp_store = array();
			//遍历商品列表
			foreach ($product_list as &$vo) {

				//
				$entity = array('p_id' => $vo['id'], 'has_sku' => $vo['has_sku'], //标志是否使用SKU信息
				'img' => $vo['main_img'], 'price' => $vo['price'], 'ori_price' => $vo['ori_price'], 'name' => $vo['name'], 'sku_id' => '', 'sku_desc' => $tmp_arr[$vo['id']], 'count' => $tmp_count[$vo['id']]//购买商品数量
				);

				if (intval($vo['has_sku']) == 1) {
					//有规格的情况下
					$entity['price'] = $tmp_arr[$vo['id']]['price'];
					$entity['sku_id'] = $tmp_arr[$vo['id']]['sku_id'];
					$entity['ori_price'] = $tmp_arr[$vo['id']]['ori_price'];
					if (!empty($tmp_arr[$vo['id']]['icon_url'])) {
						$entity['img'] = $tmp_arr[$vo['id']]['icon_url'];
					}
				}

				if (!isset($tmp_store[$vo['storeid']])) {
					$tmp_store[$vo['storeid']] = array('products' => array(), 'post_price' => 0.0, 'total_price' => 0.0);
				}

				$all_price += ($entity['count'] * $entity['price'] / 100.0);
				//每个店铺的总价
				$tmp_store[$vo['storeid']]['total_price'] += ($entity['count'] * $entity['price'] / 100.0);
				//取得该商品的运费
				$express_price = $this -> getExpressPrice($vo);
				$express_price = $express_price / 100.0;
				//转化为元
				//取得最大的运费
				//				if($tmp_store[$vo['storeid']]['post_price'] < $express_price){
				$entity['post_price'] = $express_price;
				$tmp_store[$vo['storeid']]['post_price'] += $express_price;
				$all_express += $express_price;
				//				}

				array_push($tmp_store[$vo['storeid']]['products'], $entity);
				array_push($store_ids, $vo['storeid']);
			}

			unset($map['sku_id']);
			$map['id'] = array('in', $store_ids);
			$order = " id asc ";
			$result = apiCall("Shop/Wxstore/queryNoPaging", array($map, $order));
			if (!$result['status']) {
				LogRecord($result['info'], __FILE__ . __LINE__);
				$this -> error($result['info']);
			}
			$stores = $result['info'];

			foreach ($stores as &$vo) {
				if (isset($tmp_store[$vo['id']])) {
					$tmp_store[$vo['id']]['store'] = $vo;
				}
			}

			$list = array('list' => $tmp_store, 'all_price' => $all_price, 'all_express' => $all_express, );

			session("confirm_order_info", $list);
		}

		//获取默认收货地址

		$default_address = $this -> getDefaultAddress();

		$this -> assign("default_address", $default_address);
		$this -> assign("list", $list);
		$this -> display();

	}

	/**
	 * 订单保存
	 */
	public function save() {
		//收货地址ID
		$address_id = I('post.address_id', 0);
		$province_name = I('post.province_name', '');
		$city_name = I('post.city_name', '');
		$area_name = I('post.area_name', '');
		$notes = I('post.notes', array());
		$userinfo = $this -> userinfo;
		if (IS_POST && is_array($userinfo)) {
			addWeixinLog($userinfo, '[session]saveispost');
			//购买的商品的列表
			$buyProductList = session("confirm_order_info");
			addWeixinLog($buyProductList, '购买商品的列表');

			$map = array('id' => $address_id);

			$result = apiCall("Shop/Address/getInfo", array($map));

			if (!$result['status']) {
				$this -> error($result['info']);
			}

			if (is_null($result['info'])) {
				LogRecord("收货地址信息获取失败", __FILE__ . __LINE__);
				//如果是空的
				$this -> error("收货地址信息获取失败！");
			}

			$address_info = $result['info'];
			//总价，不含运费
			$all_price = $buyProductList['all_price'];
			//运费
			$all_express = $buyProductList['all_express'];

			//订单对象
			$entity = array('wxaccountid' => $this -> wxaccount['id'], 'storeid' => 0, 'wxuser_id' => $userinfo['id'], 'price' => 0, 'mobile' => $address_info['mobile'], 'wxno' => $address_info['wxno'], 'contactname' => $address_info['contactname'], 'note' => '', 'country' => $address_info['country'], 'province' => $province_name, 'city' => $city_name, 'area' => $area_name, 'detailinfo' => $address_info['detailinfo'], 'orderid' => '', 'items' => '', );
			$i = 0;
			$ids = '';
			$orderid = $this -> getOrderID();
			//			addWeixinLog($buyProductList['list'],'订单');
			//分店铺保存订单，每个店铺一张订单
			foreach ($buyProductList['list'] as $key => $vo) {
				//店铺ID
				$entity['storeid'] = $key;
				$entity['orderid'] = $orderid . '_' . $key;
				$entity['items'] = $vo;
				//
				$price = 0.0;
				foreach ($vo['products'] as $item) {
					$price += $item['price'];
				}

				$entity['price'] = $price;
				if ($i < count($notes)) {
					$entity['note'] = $notes[$i];

				}

				$i++;
				//				dump($entity['items']['products']);
				//				exit();
				$result = apiCall("Shop/Orders/addOrder", array($entity));
				//				addWeixinLog($result,'订单3333');
				if (!$result['status']) {
					LogRecord($result['info'], __FILE__ . __LINE__);
					$this -> error($result['info']);
				}
				$ids .= $result['info'] . '-';
			}
			$ids = trim($ids, "-");
			//TODO: 从购物车中移除相对应的商品，根据店铺ID，商品ID，商品SKU
			//目前 就直接删除购物车
			session("shoppingcart", null);
			session("confirm_order_info", null);
			//			addWeixinLog($ids,'订单IDs');
			$this -> success("订单保存成功，前往支付！", C('SITE_URL') . "/index.php/Shop/OnlinePay/pay/id/$ids?showwxpaytitle=1");

			//			$this->success("订单保存成功，前往支付！",C('SITE_URL')."/index.php?m=Shop&c=OnlinePay&a=pay&id=$ids&showwxpaytitle=1");
		} else {
			LogRecord("禁止访问！", __FILE__ . __LINE__);
			$this -> error("禁止访问！");
		}

	}

	//===================private

	/**
	 * 获取商品集合
	 * @param $p_id_arr 商品id数组
	 */
	private function getProductList($p_id_arr) {
		$map = array();
		$map['id'] = array('in', $p_id_arr);
		$order = " id desc ";
		$result = apiCall("Shop/Wxproduct/queryNoPaging", array($map, $order));

		if (!$result['status']) {
			LogRecord($result['info'], __FILE__ . __LINE__);
			$this -> error($result['info']);
		}

		return $result['info'];
	}

	private function getDefaultAddress() {
		$result = apiCall("Shop/Address/getInfo", array( array('wxuserid' => $this -> userinfo['id'], 'default' => 1)));
		if (!$result['status']) {
			LogRecord($result['info'], __FILE__ . __LINE__);
		}

		$default_address = $result['info'];
		//默认收货地址
		$province_one = apiCall("Tool/Province/getInfo", array( array("provinceID" => $default_address['province'])));
		$city_one = apiCall("Tool/City/getInfo", array( array("cityID" => $default_address['city'])));
		$area_one = apiCall("Tool/Area/getInfo", array( array("areaID" => $default_address['area'])));

		if (is_array($province_one['info'])) {
			$default_address['province_name'] = $province_one['info']['province'];
		}
		if (is_array($city_one['info'])) {
			$default_address['city_name'] = $city_one['info']['city'];
		}
		if (is_array($area_one['info'])) {
			$default_address['area_name'] = $area_one['info']['area'];
		}

		return $default_address;
	}

	private function getOrderID() {
		return date('YmdHis', time()) . $this -> randInt();
	}

	private function randInt() {
		srand(GUID());
		return rand(10000000, 99999999);
	}

	/**
	 * 获取商品的运费
	 */
	private function getExpressPrice($product) {

		if (intval($product['attrext_ispostfree']) == 1) {
			return 0;
		}

		if (intval($product['delivery_type']) == 0) {
			//快递，EMS，
			$express = json_decode($product['express']);

			if (count($express) > 0) {
				return ($express[0] -> price);
			} else {
				return 0;
			}

		} elseif (intval($product['delivery_type']) == 1) {
			//使用运费模板
		}

		return 0;
	}

}
