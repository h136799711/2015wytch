<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Plugin;

abstract class WeixinPlugin {
	/**
	 * @param $data 通常包含是微信服务器返回来的信息
	 * @return 返回 Wechat可处理的数组
	 */
	abstract function process($data);
	
	/*
	 * 组装返回的数据
	 * 文本
	 */
	protected function _text($text){
		return array($text,'text');
	}
	
	/*
	 * 组装返回的数据
	 * 图文
	 * $news 格式：
	 * array(
	 * 	array('title标题','description图文描述','图片地址picurl','跳转链接url'),
	 * 	array('title标题','description图文描述','图片地址picurl','跳转链接url'),
	 * )
	 */
	protected function _news($news){
		return array($news,'news');
	}
	
	protected function _image($media_id){
		return array($media_id,'image');
	}
	
	
}
