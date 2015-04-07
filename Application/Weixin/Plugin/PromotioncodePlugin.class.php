<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Plugin;

/**
 * 推广二维码插件
 * 
 */
class PromotioncodePlugin extends  WeixinPlugin{
	
	/**
	 * @param $data 通常包含是微信服务器返回来的信息
	 * @return 返回 Wechat可处理的数组
	 */
	function process($data){
		addWeixinLog($data,'[PromotioncodePlugin]');
		$promotionapi = new \Common\Api\PromotioncodeApi(C('PROMOTIONCODE'));
		
		if(empty($data['fans']) ){
			LogRecord("fans参数为empty", "[PromotioncodePlugin]".__LINE__);
			return array("1二维码推广插件[调用失败]","text");
		}
		
		if(empty($data['wxaccount']) ){
			LogRecord("wxaccount参数为empty", "[PromotioncodePlugin]".__LINE__);
			return array("2二维码推广插件[调用失败]","text");
		}
		
		$result = $promotionapi->process($data['wxaccount']['appid'], $data['wxaccount']['appsecret'],$data['fans']);
		if($result['status']){
			return array($result['info'],"image");
		}else{
			return array($result['info'],"text");
		}

	}

}
