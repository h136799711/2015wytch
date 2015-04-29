<?php

namespace Shop\Controller;
use Think\Controller;

use Common\Api\Wxpay;

class WxpayTestController extends Controller{
	
	public  function test1(){
		$p_ids = array(1,2,3,4,5,6);
		$result = apiCall("Shop/Orders/monthlySales", array($p_ids));
	
		dump($result);
		
	}
	
	
	public function testTask(){
		$this->display();
	}
	
	public function jsapi(){
		$jsApiParameters = "";
		
		$config = C("WXPAY_CONFIG");
		//①、获取用户openid
		$tools = new \Common\Api\Wxpay\JsApi($config);
		$openId = $tools->GetOpenid();
//		dump($openId);
		
		//②、统一下单
		$input = new \Common\Api\Wxpay\WxPayUnifiedOrder();
		$input->setConfig($config);
//		dump($input);
		$input->SetBody("test2");
		$input->SetAttach("test2");
		$input->SetOut_trade_no('10027619'.date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
//		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://2test.8raw.com/index.php/Shop/WxpayNotify/index");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		\Common\Api\Wxpay\WxPayApi::setConfig($config);
		$order = \Common\Api\Wxpay\WxPayApi::unifiedOrder($input);
//		dump($order);
		$jsApiParameters = $tools->GetJsApiParameters($order);
		$this->assign("jsApiParameters",$jsApiParameters);
		$this->display();
	//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
	
	}	
}
