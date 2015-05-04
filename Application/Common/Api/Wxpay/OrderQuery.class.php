<?php


namespace Common\Api\Wxpay;

require_once ("WxPayData.class.php");

class OrderQuery {
	
	private $config ;
	function __construct($config){
		$this->config = $config;
	}
	
	/**
	 * 查询订单根据out_trade_no
	 */
	public function  queryByOutTradeNo($out_trade_no){
		
		$input = new WxPayOrderQuery();
		$input->setConfig($this->config);
		$input->SetOut_trade_no($out_trade_no);
		\Common\Api\Wxpay\WxPayApi::setConfig($this->config);
		return \Common\Api\Wxpay\WxPayApi::orderQuery($input);
	}
	
	
}
