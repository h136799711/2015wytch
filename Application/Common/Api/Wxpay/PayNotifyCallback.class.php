<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Common\Api\Wxpay;

class PayNotifyCallback extends WxPayNotify {
	
	function __construct($config){
//		$this->config = array($this->config,$config);
		parent::__construct($config);
	}
	
//	//查询订单
//	public function Queryorder($transaction_id)
//	{
//		$input = new WxPayOrderQuery();
//		$input->setConfig($this->config);
//		$input->SetTransaction_id($transaction_id);
//		WxPayApi->setConfig($this->config);
//		$result = WxPayApi::orderQuery($input);
//		
//		if(array_key_exists("return_code", $result)
//			&& array_key_exists("result_code", $result)
//			&& $result["return_code"] == "SUCCESS"
//			&& $result["result_code"] == "SUCCESS")
//		{
//			return true;
//		}
//		return false;
//	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{

		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
//		if(!$this->Queryorder($data["transaction_id"])){
//			$msg = "订单查询失败";
//			return false;
//		}
		
		//支付成功通知地址
//		$url = "http://2test.8raw.com/index.php/Shop/WxpayNotify/aysncNotify?key=hebidu";
		$url = $this->config['PROCESS_URL'];
		addWeixinLog(json_encode($data),"[成功通知处理数据结构]");
		
		$entity = array();
		
		fsockopenRequest($url, $data);
		//TODO: 异步处理流程
	
//	<xml><appid><![CDATA[wx5f9ed360f5da5370]]></appid>
//<attach><![CDATA[13_]]></attach>
//<bank_type><![CDATA[CFT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[Y]]></is_subscribe>
//<mch_id><![CDATA[1238878502]]></mch_id>
//<nonce_str><![CDATA[lgejoe9g234qsz67lwk5qhoafv4nuvz9]]></nonce_str>
//<openid><![CDATA[oxGH0sgeUkH4g8aowy0452xJnX1o]]></openid>
//<out_trade_no><![CDATA[2015042820173392481208_2]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[1DFD346C8F496CCC7D45CF7031B0257C]]></sign>
//<time_end><![CDATA[20150428201758]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[1009360424201504280096239638]]></transaction_id>
//</xml>
		return true;
	}
	
	
}
