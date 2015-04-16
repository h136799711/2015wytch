<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Weixin\Controller;

class WeixinjsController extends WeixinController {
	private $wxapi;
	
	protected function getCurrentURL(){
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		return $url;
	}
	/**
	 * weixinjs
	 */
	public function index() {
		C('SHOW_PAGE_TRACE',false);
		$token = I('get.tk','');
//		dump($token);
		$result = apiCall("Weixin/Wxaccount/getInfo", array(array('token'=>$token)));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
			return "无法获取！";
		}
		if(is_null($result['info'])){
			return "Token 无效！";
		}
		$this->wxapi = new \Common\Api\WeixinApi($result['info']['appid'],$result['info']['appsecret']);
		$url = I('get.url','','urldecode');
//		addWeixinLog($url,"weixinjs url");
		$signPackage = $this->wxapi -> getSignPackage($url);
//		addWeixinLog($signPackage,"wsignPackage");
		$jsapilist = "'translateVoice','onVoicePlayEnd',
		'uploadVoice','downloadVoice','chooseImage','previewImage','uploadImage',
		'downloadImage','getNetworkType','openLocation',
		'getLocation','chooseCard','openCard','addCard','openProductSpecificView','chooseWXPay','scanQRCode','chooseImage','previewImage','uploadImage',
		'downloadImage','showMenuItems','showAllNonBaseMenuItem','hideAllNonBaseMenuItem','hideOptionMenu','showOptionMenu','hideMenuItems',
		'onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','hideAllNonBaseMenuItem','closeWindow',
		'showAllNonBaseMenuItem','startRecord','stopRecord','onVoiceRecordEnd','playVoice','pauseVoice','stopVoice'";
//		$jsapilist = "'onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo'";
		$config = "wx.config({
			    debug: false, 
			    appId: '" . $signPackage["appId"] . "', 
			    timestamp: '" . $signPackage["timestamp"] . "', 
			    nonceStr: '" . $signPackage["nonceStr"] . "', 
			    signature: '" . $signPackage["signature"] . "',
			    jsApiList: [" . $jsapilist . "]
			});";
		echo ($config);
	}
	
}
