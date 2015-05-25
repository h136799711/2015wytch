<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;
use Think\Controller;
class OnlinePayController extends ShopController {
	
//	protected function __initialize(){
//		// 获取配置
//		$this -> getConfig();
//
//		if (!defined('APP_VERSION')) {
//			//定义版本
//			if (defined("APP_DEBUG") && APP_DEBUG) {
//				define("APP_VERSION", time());
//			} else {
//				define("APP_VERSION", C('APP_VERSION'));
//			}
//		}
//		C('SHOW_PAGE_TRACE', false);//设置不显示trace
//	}
	
	/**
	 * 更改订单为货到付款
	 */
	public function cashOndelivery() {
		
		$ids = I('post.id', 0);
		$ids = rtrim($ids, "-");
		$ids = split("-", $ids);
//		$map['id'] = array('in', $ids);
//		$map['pay_status'] = 0;
		$result = serviceCall("Common/Order/cashOndelivery", array($ids,false,$this->userinfo['id']));
		
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$wxuserid = $this->userinfo['id'];
		
		$text = "用户ID:$wxuserid,时间:" . date("Y-m-d H:i:s",time()) . ",订单ID:" . rtrim(I('post.id', 0),"-") . ",选择了货到付款,请登录后台查看订单。";
		$token = C('SHOP_TOKEN');
		$this->sendToWxaccount($token, $text);

		$this -> success("操作成功！");
	}
	
	private function sendToWxaccount($token, $text) {
		$result = apiCall("Shop/Wxaccount/getInfo", array(array('token' => $token)));
		if ($result['status']) {
			$wxapi = new \Common\Api\WeixinApi($result['info']['appid'], $result['info']['appsecret']);
			$map = array('name' => "WXPAY_OPENID");
			$result = apiCall("Admin/Config/getInfo", array($map));
			
			addWeixinLog($result, "接收订单支付成功的OPENID");
			if ($result['status']) {
				$openidlist = split(",", $result['info']['value']);
				foreach ($openidlist as $openid) {
					$wxapi -> sendTextToFans($openid, $text);
				}
			}
		} else {
			LogRecord($result['info'], __FILE__ . __LINE__ . "货到付款成功消息失败");
		}
	}
	/**
	 * 微信支付页面
	 */
	public function pay() {
		//订单ID
		$ids = I('get.id', 0);

		$ids = rtrim($ids, "-");
		$ids_arr = split("-", $ids);
		if (count($ids_arr) == 0) {
			$this -> error("参数错误！");
		}
		$map = array();
		$map['id'] = array('in', $ids_arr);
		$result = apiCall("Shop/OrdersInfoView/queryNoPaging", array($map));
		//TODO: 判断订单状态是否为待支付
		if ($result['status']) {

			$order_list = $result['info'];
			
			$payConfig = C('WXPAY_CONFIG');
			$payConfig['jsapicallurl'] = getCurrentURL();

//			addWeixinLog($payConfig, 'payConfig');
			$items = array();
			$total_fee = 0;
			$total_express = 0.0;
			$body = "";
			$attach = "";
			
			foreach ($order_list as $order) {
				$trade_no = $order['orderid'];
				$total_fee +=($order['price']);
				
				$products = $this -> getProducts($order[id]);
				foreach ($products as $vo) {
					$total_express += $vo['post_price'];
					if(empty($body)){
						$body = $vo['name'];
					}
				}
				$attach .= $order['id'].'_';
				array_push($items, $item);
			}
			$total_fee = $total_fee + $total_express;
			if ($total_fee <= 0) {
				$this -> error("支付金额不能小于0！");
			}
			
//			$total_fee = 1;
			
			//测试时
			$this -> setWxpayConfig($payConfig, $trade_no, $body, $total_fee,$attach);
			$this -> assign("total_express", $total_express);
			$this -> assign("ids", I('get.id', 0));
			$this -> assign("total_fee", ($total_fee + $total_express));
			//			$this -> assign("items",$items);
			$this -> display();

		} else {
			$this -> error("支付失败！");
		}

	}

	//====================PRIVATE===

	/**
	 * 获取订单的商品信息
	 * @param $orders_id 订单ID
	 */
	private function getProducts($orders_id) {
		$map = array('orders_id' => $orders_id);
		$result = apiCall("Shop/OrdersItem/queryNoPaging", array($map));
		if (!$result['status']) {
			LogRecord($result['info'], __FILE__ . __LINE__);
			$this -> error($result['info']);
		}

		return $result['info'];
	}

	/**
	 *
	 * @param config 微信支付配置
	 * @param trade_no 订单ID
	 * @param itemdesc 商品描述
	 * @param total_fee 总价格
	 */
	private function setWxpayConfig($config, $trade_no, $body, $total_fee, $attach='') {
		try {
			$jsApiParameters = "";
			//①、获取用户openid
			$tools = new \Common\Api\Wxpay\JsApi($config);
			
//			$openId = $tools -> GetOpenid();
			$openId = $this->openid;
			//②、统一下单
			$input = new \Common\Api\Wxpay\WxPayUnifiedOrder();
			$input -> setConfig($config);
			$input -> SetBody($body);//string(32)
			$input -> SetAttach($attach);//
			$input -> SetOut_trade_no($trade_no);
			$input -> SetTotal_fee($total_fee);
			$input -> SetTime_start(date("YmdHis"));
			$input -> SetTime_expire(date("YmdHis", time() + 60*30));
//			$input -> SetGoods_tag("test");
			$input -> SetNotify_url($config['NOTIFYURL']);
			$input -> SetTrade_type("JSAPI");
			$input -> SetOpenid($openId);
			\Common\Api\Wxpay\WxPayApi::setConfig($config);
			$order = \Common\Api\Wxpay\WxPayApi::unifiedOrder($input);
			$jsApiParameters = $tools -> GetJsApiParameters($order);
			$this -> assign("jsApiParameters", $jsApiParameters);
			
		} catch(WxPayException $sdkexcep) {
			$error = $sdkexcep -> errorMessage();
			$this -> assign("error", $error);
		}

	}

}
