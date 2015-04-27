<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

class OnlinePayController extends ShopController{
	
	
	/**
	 * 更改订单为货到付款
	 */
	public function cashOndelivery(){
		//TODO: 货到付款
		
		$ids = I('post.id', 0);
		$ids = rtrim($ids,"-");
		$ids = split("-", $ids);
		$map['id'] = array('in',$ids);
		$map['pay_status'] = 0;
		$result = apiCall("Shop/Orders/savePayStatus",array($map,\Common\Model\OrdersModel::ORDER_CASH_ON_DELIVERY));
		

		if(!$result['status']){
			$this->error($result['info']);
		}
		$this->success("操作成功！");
	}
	
	/**
	 * 微信支付页面
	 */
	public function wxpay(){
		//订单ID
		$ids = I('get.id', 0);		
		$ids = rtrim($ids,"-");
		$ids = split("-", $ids);
		if(count($ids) == 0){
			$this->error("参数错误！");
		}
		$map = array();
		$map['id'] = array('in' , $ids);
		$result = apiCall("Shop/OrdersInfoView/queryNoPaging", array($map));
		
		if ($result['status']) {
			
			$order_list = $result['info'];
			addWeixinLog($order_list,'支付订单');
			$payConfig = C('WXPAY_CONFIG');
			$payConfig['jsapicallurl'] = $this->getCurrentURL();
			
			$items = array();
			$total_fee = 0.0;
			$total_express = 0.0;
			
			foreach($order_list as $order){
				$item = json_decode($order['items'],JSON_UNESCAPED_UNICODE);
				$total_fee += $item['total_price'];
				$total_express += $item['post_price'];
				array_push($items,$item);
			}
			
			if($total_fee <= 0){
				$this->error("支付金额不能小于0！");
			}
			$trade_no = rtrim($ids,"-");
			$total_fee = 1;//测试时
			$this->setWxpayConfig($config, $trade_no, $itemdesc, $total_fee);
			$this -> assign("total_express",$total_express);
			$this -> assign("ids",I('get.id', 0));
			$this -> assign("total_fee",($total_fee+$total_express));
//			$this -> assign("items",$items);
			$this -> display();

		}else{
			$this->error("支付失败！");
		}

	}
	
	//====================PRIVATE===
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
	
	
	
}
