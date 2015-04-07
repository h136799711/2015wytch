<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Api;

class SDKRuntimeException extends \Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
class WxpayCommonApi {
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	protected $APPID = '';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	protected $APPSECRET = '';
	//受理商ID，身份标识
	protected $MCHID = '';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	protected $KEY = '';
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	protected $jsapicallurl = '';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	protected $SSLCERT_PATH = '';
	protected $SSLKEY_PATH = '';
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	protected $NOTIFY_URL = '';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	protected $CURL_TIMEOUT = 30;
	
	function __construct($config){
		if(method_exists($this, 'init') ){
      		$this->init();
  		}
		$this->setConfig($config);
	}
	
	protected function setConfig($config){
		$this->APPID = $config['appid'];
		$this->APPSECRET = $config['appsecret'];
		$this->MCHID = $config['mchid'];
		$this->KEY = $config['key'];
		$this->jsapicallurl = $config['jsapicallurl'];
		$this->SSLCERT_PATH = $config['sslcertpath'];
		$this->SSLKEY_PATH = $config['sslkeypath'];
		$this->NOTIFY_URL = $config['notifyurl'];
	}
	
	function trimString($value) {
		$ret = null;
		if (null != $value) {
			$ret = $value;
			if (strlen($ret) == 0) {
				$ret = null;
			}
		}
		return $ret;
	}

	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public function createNoncestr($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode) {
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}
		return $reqPar;
	}

	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj) {
		foreach ($Obj as $k => $v) {
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this -> formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String . "&key=" . $this->KEY;
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}

	/**
	 * 	作用：array转xml
	 */
	function arrayToXml($arr) {
		$xml = "<xml>";
		foreach ($arr as $key => $val) {
			if (is_numeric($val)) {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";

			} else
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
		}
		$xml .= "</xml>";
		return $xml;
	}

	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml) {
		//将XML转为array
		$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array_data;
	}

	/**
	 * 	作用：以post方式提交xml到对应的接口url
	 */
	public function postXmlCurl($xml, $url, $second = 30) {
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $second);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		curl_close($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error" . "<br>";
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}

	/**
	 * 	作用：使用证书，以post方式提交xml到对应的接口url
	 */
	function postXmlSSLCurl($xml, $url, $second = 30) {
		$ch = curl_init();
		//超时时间
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
		curl_setopt($ch, CURLOPT_SSLCERT, $this->SSLCERT_PATH);
		//默认格式为PEM，可以注释
		curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
		curl_setopt($ch, CURLOPT_SSLKEY, $this->SSLKEY_PATH);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error" . "<br>";
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}

	/**
	 * 	作用：打印数组
	 */
	function printErr($wording = '', $err = '') {
		print_r('<pre>');
		echo $wording . "</br>";
		var_dump($err);
		print_r('</pre>');
	}
	
	
	
	

}
