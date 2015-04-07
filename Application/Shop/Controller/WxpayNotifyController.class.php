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
	 * 微信支付成功，通知接口
	 */
	public function index() {

		$config = C('WXPAY_CONFIG');
		//使用通用通知接口
		$notify = new \Common\Api\NotifyApi($config);

		//      //存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify -> saveData($xml);

		addWeixinLog($xml, '[notify]xml');
		$entity = array();
		$flag = false;
		if ($notify -> checkSign() == TRUE) {
			if ($notify -> data["return_code"] == "FAIL") {

				//此处应该更新一下订单状态，商户自行增删操作
				addWeixinLog($notify -> data["return_msg"], "微信支付-【通信出错】");
				LogRecord($notify -> data['return_msg'], "微信支付－[通信出错]");

			} else {
				$entity['appid'] = $notify->data['appid'];
				$entity['mch_id'] = $notify->data['mch_id'];
				$entity['nonce_str'] = $notify->data['nonce_str'];
				$entity['sign'] = $notify->data['sign'];
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
//					LogRecord("out_trade_no ".$entity['out_trade_no'].",transaction_id:".$entity['transaction_id'], "微信支付－[支付成功]");
					
					$orderid = $entity['out_trade_no'];
					
					//1. 根据订单id来更新订单状态
					$result = apiCall("Shop/Orders/getInfo", array(array('orderid'=>$orderid)));
					//清除缓存
					$fanskey = "appid_".$entity['appid']."_" . $entity['openid'];
					S($fanskey,null);
					session("userinfo",null);
					//2. 查询订单是否已更新
					addWeixinLog($result,"[完成支付的订单信息]");
					if($result['status'] && is_array($result['info'])){
						$paidStatus = \Common\Model\OrdersModel::ORDER_PAID;
						if($result['info']['pay_status'] != $paid){//订单不为已支付的情况下更新
							
							$wxuserid =  $result['info']['wxuser_id'];
							$wxaccountid =  $result['info']['wxaccountid'];
							$entity['wxuserid'] = $wxuserid;
							//5. 升级用户的用户组
							$result = apiCall("Admin/Wxuser/groupUp",array($wxuserid));
							if(!$result['status']){
								LogRecord($wxuserid, "[升级用户的用户组失败]");
							}
							
							//3. 更新为已支付，（对数据行要加写锁）
							$result = apiCall("Shop/Orders/savePayStatus",array($orderid,$paidStatus));
							if(!$result['status']){
								LogRecord($result['info'], "[更新订单支付状态]");
							}else{
								$map = array('id'=>$wxuserid);
								$addScore = intval(floatval($entity['total_fee'])/100.0);
								if($addScore > 0){
									//4. 更新用户积分 ＋ 消费金额
									$result = apiCall("Admin/Wxuser/setInc", array($map,"score",$addScore));
									if(!$result['status']){
										LogRecord($result['info'], "[增加用户积分]");							
									}
								}
								
								
							}
							
							addWeixinLog($result,"[处理微信支付成功通知的处理都已成功！]");
							$flag = true;
						}
					}
				}
			}

			//纪录支付回发消息到数据库中
			$result = apiCall("Shop/OrderHistory/add",array($entity));
			if(!$result['status']){
				LogRecord($result['info'].";out_trade_no ".$entity['out_trade_no'].",transaction_id:".$entity['transaction_id'], "OrderHistory－[写入数据库失败]");
			}else{
				addWeixinLog($result,"[纪录支付回发消息成功！]");
			}
						
			$notify -> setReturnParameter("return_code", "SUCCESS");
			//设置返回码
		} else {
			$notify -> setReturnParameter("return_code", "FAIL");
			//返回状态码
			$notify -> setReturnParameter("return_msg", "签名失败");
			//返回信息
		}

		$returnXml = $notify -> returnXml();

		echo $returnXml;
		
		if($flag){			
			//6. TODO: 发送提醒消息给指定微信号		
			
			$text = "用户ID:$wxuserid,时间:".$entity['time_end'].",订单号:".$entity['out_trade_no'] .",已支付,请关注订单。";
			
				addWeixinLog($text,"[发送支付成功提醒消息给指定微信号！]");
			$this->sendTextTo($text,$wxaccountid);
		}

	}
	/**
	 * TODO: 后期考虑使用消息队列方式。循环查数据库
	 */
	private function sendTextTo($text,$wxaccountid){
		$result = apiCall("Admin/Wxaccount/getInfo", array(array('id'=>$wxaccountid)));
		if($result['status']){
			$wxapi = new \Common\Api\WeixinApi($result['info']['appid'],$result['info']['appsecret']);
			$map = array('name'=>"WXPAY_OPENID");
			$result =apiCall("Admin/Config/getInfo", array($map));
			if($result['status']){
				$openidlist = split(",",$result['info']['value']);
				addWeixinLog($openidlist,"接收订单支付成功的OPENID");
				foreach($openidlist as $openid){
					$wxapi->sendTextToFans($openid,$text);
				}
			}
		}else{
			LogRecord($result['info'], __FILE__.__LINE__."发送支付成功消息失败");
		}
	}

}
