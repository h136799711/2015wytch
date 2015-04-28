<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;
use Think\Controller;

class WxpayNotifyController extends Controller {
	
	/**
	 * 2015 0428最新接口
	 */
	public function index(){
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		addWeixinLog($xml, ' 20150428最新接口 [支付成功通知]');
		
		$config = C('WXPAY_CONFIG');
		$notify = new \Common\Api\Wxpay\PayNotifyCallback($config);
		addWeixinLog($notify, ' 20150428最新接口 [支付成功通知]2');
//		$notify = new \Common\Api\Wxpay\WxPayNotify($config);
//		dump($notify);
		$notify->Handle(false);
	}
	public function test(){
		$config = C('WXPAY_CONFIG');
		$notify = new \Common\Api\Wxpay\PayNotifyCallback($config);
		$msg = "";
		dump($notify->NotifyProcess(array('transaction_id'=>'22333'),$msg));
		dump($msg);
	}
	

	/**
	 * 微信支付成功，通知接口
	 */
	public function old() {

		$config = C('WXPAY_CONFIG');
		//使用通用通知接口
		$notify = new \Common\Api\NotifyApi($config);

		//      //存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify -> saveData($xml);

		addWeixinLog($xml, '[notify] xml');
		$entity = array();
		$flag = false;
//		$orderids = "";
		if ($notify -> checkSign() == TRUE) {
			if ($notify -> data["return_code"] == "FAIL") {

				//此处应该更新一下订单状态，商户自行增删操作
				addWeixinLog($notify -> data["return_msg"], "微信支付-【通信出错】");
				LogRecord($notify -> data['return_msg'], "微信支付－[通信出错]");

			} else {
				$entity['appid'] = $notify -> data['appid'];
				$entity['mch_id'] = $notify -> data['mch_id'];
				$entity['nonce_str'] = $notify -> data['nonce_str'];
				$entity['sign'] = $notify -> data['sign'];
				if ($notify -> data["result_code"] == "FAIL") {

					$entity['result_code'] = $notify -> data['result_code'];
					$entity['err_code'] = $notify -> data['err_code'];
					$entity['err_code_des'] = $notify -> data['err_code_des'];
					//此处应该更新一下订单状态，商户自行增删操作
					addWeixinLog($entity['err_code_des'], "微信支付-业务出错");
					LogRecord($entity['err_code_des'], "微信支付－[业务出错]");

				} else {
					$entity['openid'] = $notify -> data['openid'];
					$entity['is_subscribe'] = $notify -> data['is_subscribe'];
					$entity['trade_type'] = $notify -> data['trade_type'];
					$entity['bank_type'] = $notify -> data['bank_type'];
					$entity['total_fee'] = $notify -> data['total_fee'];
					$entity['coupon_fee'] = $notify -> data['coupon_fee'];
					$entity['fee_type'] = $notify -> data['fee_type'];
					$entity['transaction_id'] = $notify -> data['transaction_id'];
					$entity['fee_type'] = $notify -> data['fee_type'];
					$entity['out_trade_no'] = $notify -> data['out_trade_no'];
					$entity['attach'] = $notify -> data['attach'];
					$entity['time_end'] = $notify -> data['time_end'];
					//此处应该更新一下订单状态，商户自行增删操作
					addWeixinLog("【支付成功】", "微信支付");
					try{
//						$entity['wxuserid'] = $this -> whenSuccess($entity);
					}catch(Exception $ex){
						//不做处理
					}
				}
			}
			
			//纪录支付回发消息到数据库中
			$result = apiCall("Shop/OrderHistory/add", array($entity));
			if (!$result['status']) {
				LogRecord($result['info'] . ";out_trade_no " . $entity['out_trade_no'] . ",transaction_id:" . $entity['transaction_id'], "OrderHistory－[写入数据库失败]");
			} else {
				addWeixinLog($result, "[纪录支付回发消息成功！]");
			}

			$notify -> setReturnParameter("return_code", "SUCCESS");
			//设置返回码
		} else {
			$notify -> setReturnParameter("return_code", "FAIL");
			//返回状态码
			$notify -> setReturnParameter("return_msg", "签名失败");
			//返回信息
			addWeixinLog("签名失败");
		}

		$returnXml = $notify -> returnXml();

		echo $returnXml;


	}

	/**
	 * 支付成功通知
	 */
	public function whenSuccess($entity) {
		$orderids = $entity['attach'];
		addWeixinLog($orderids, "[完成支付的订单ID]");
		//1. 清除缓存
		$fanskey = "appid_" . $entity['appid'] . "_" . $entity['openid'];
		S($fanskey, null);
		session("userinfo", null);
		//2. 获取订单信息
		$orderids = split("-", $orderids);	
		addWeixinLog($orderids, "[orderids]");
		$map = array('pay_status'=>\Common\Model\OrdersModel::ORDER_TOBE_PAID,'orderid' => array('in',$orderids));
		//只查询待支付的订单信息
		$result = apiCall("Shop/Orders/queryNoPaging", array( $map));		
		addWeixinLog($result, "[通知支付完成的订单]");
		$wxuserid = 0;
		//3. 判断订单信息是否获取到
		if ($result['status'] && is_array($result['info'])) {
				
				$orders = $result['info'];
				$wxuserid = $orders[0]['wxuser_id'];
				$wxaccountid = $orders[0]['wxaccountid'];
				
				
				//改变订单的状态为已支付
				$paidStatus = \Common\Model\OrdersModel::ORDER_PAID;
				$result = apiCall("Shop/Orders/savePayStatus", array($map, $paidStatus));
				
				if(!$result['status']){
//					LogRecord($result['info'], __FILE__.__LINE__);
					addWeixinLog($result['info'],__FILE__.__LINE__);
				}
				
				
				
				//.获取店铺信息，订单商品总价，不含运费================================================================================
				$total_price = 0;//总价格 分
				$total_items = 0;//商品总数量 
				$stores = array();//店铺信息
				foreach($orders as $vo){
					//遍历订单
					//1. 获取订单的商品列表
					$items = json_decode($vo['items'],JSON_UNESCAPED_UNICODE);
					$total_price += ($vo['total_price']);
					foreach($items['products'] as $product){
						$total_items += $product['count'];
					}
					
					//增加店铺经验
					$result = apiCall("Shop/Wxstore/setInc", array(array('id'=>$vo['storeid']), "exp", $total_items));	
					if(!$result['status']){
						LogRecord($result['info'], __FILE__.__LINE__);
					}
					array_push($stores,$items['store']);
				}
				//================================================================================
				
				
				
				
				
				//1. 增加用户积分
				//================================================================================
				$addScore = round( $total_price );				
				$map = array('id'=>$wxuserid);
				$result = apiCall("Shop/Wxuser/setInc", array($map, "score", $addScore));	
				
				//2. 增加经验
				$result = apiCall("Shop/Wxuser/setInc", array($map, "exp", $total_items));		
				//================================================================================
				
				
				
				
				
				
				
				addWeixinLog($result, "[处理微信支付成功通知的处理都已成功！]");
				
				//LAST: 发送支付成功提醒消息
				$text = "用户ID:$wxuserid,时间:" . $entity['time_end'] . ",订单ID:" . $entity['out_trade_no'] . ",已支付,请登录后台查看订单。";
				addWeixinLog($text, "[发送支付成功提醒消息给指定微信号！]");
				$this->sendNotification($stores, $wxaccountid, $text);
		}
		
		return $wxuserid;
	}

	/**
	 * 1. 发送通知消息给店铺
	 * 2. 发送通知消息给公众号人员
	 * TODO: 需要优化
	 */
	private function sendNotification($stores,$wxaccountid,$text){
		//TODO: 	
		//1. 发送给店铺
//		$this -> sendToStores($stores，$text);
		
		//2. 发送给配置的微信粉丝
		$this -> sendToWxaccount($wxaccountid,$text);
	}
	
	
	
	//
	private function sendTextTo($wxaccountid,$text) {
		$result = apiCall("Shop/Wxaccount/getInfo", array( array('id' => $wxaccountid)));
		if ($result['status']) {
			$wxapi = new \Common\Api\WeixinApi($result['info']['appid'], $result['info']['appsecret']);
			$map = array('name' => "WXPAY_OPENID");
			$result = apiCall("Admin/Config/getInfo", array($map));
			if ($result['status']) {
				$openidlist = split(",", $result['info']['value']);
				addWeixinLog($openidlist, "接收订单支付成功的OPENID");
				foreach($openidlist as $openid) {
					$wxapi -> sendTextToFans($openid, $text);
				}
			}
		} else {
			LogRecord($result['info'], __FILE__ . __LINE__ . "发送支付成功消息失败");
		}
	}

}
