<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Api;

/**
 * 微信接口调用封装类
 * @author hebiduhebi@163.com
 */
class WeixinApi {

	//
	protected $errcode = array("-1" => "系统繁忙，此时请开发者稍候再试", "0" => "请求成功", "40001" => "获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口", "40002" => "不合法的凭证类型", "40003" => "不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID", "40004" => "不合法的媒体文件类型", "40006" => "不合法的文件大小", "40005" => "不合法的文件类型", "40007" => "不合法的媒体文件id", "40008" => "不合法的消息类型", "40009" => "不合法的图片文件大小", "40010" => "不合法的语音文件大小", "40011" => "不合法的视频文件大小", "40012" => "不合法的缩略图文件大小", "40013" => "不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写", "40014" => "不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口", "40015" => "不合法的菜单类型", "40016" => "不合法的按钮个数", "40017" => "不合法的按钮个数", "40018" => "不合法的按钮名字长度", "40019" => "不合法的按钮KEY长度", "40020" => "不合法的按钮URL长度", "40021" => "不合法的菜单版本号", "40022" => "不合法的子菜单级数", "40023" => "不合法的子菜单按钮个数", "40024" => "不合法的子菜单按钮类型", "40025" => "不合法的子菜单按钮名字长度", "40026" => "不合法的子菜单按钮KEY长度", "40027" => "不合法的子菜单按钮URL长度", "40028" => "不合法的自定义菜单使用用户", "40029" => "不合法的oauth_code", "40030" => "不合法的refresh_token", "40031" => "不合法的openid列表", "40032" => "不合法的openid列表长度", "40033" => "不合法的请求字符，不能包含\uxxxx格式的字符", "40035" => "不合法的参数", "40039" => "不合法的URL长度", "40038" => "不合法的请求格式", "40050" => "不合法的分组id", "40051" => "分组名字不合法", "41001" => "缺少access_token参数", "41002" => "缺少appid参数", "41003" => "缺少refresh_token参数", "41004" => "缺少secret参数", "41005" => "缺少多媒体文件数据", "41006" => "缺少media_id参数", "41007" => "缺少子菜单数据", "41008" => "缺少oauth code", "41009" => "缺少openid", "42001" => "access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明", "42002" => "refresh_token超时", "42003" => "oauth_code超时", "43001" => "需要GET请求", "43002" => "需要POST请求", "43003" => "需要HTTPS请求", "43004" => "需要接收者关注", "43005" => "需要好友关系", "44001" => "多媒体文件为空", "44002" => "POST的数据包为空", "44003" => "图文消息内容为空", "44004" => "文本消息内容为空", "45001" => "多媒体文件大小超过限制", "45003" => "标题字段超过限制", "45002" => "消息内容超过限制", "45004" => "描述字段超过限制", "45005" => "链接字段超过限制", "45006" => "图片链接字段超过限制", "45007" => "语音播放时间超过限制", "45008" => "图文消息超过限制", "45009" => "接口调用超过限制", "45010" => "创建菜单个数超过限制", "45015" => "回复时间超过限制", "45016" => "不允许修改", "45017" => "分组名字过长", "45018" => "分组数量超过上限", "46001" => "不存在媒体数据", "46002" => "不存在的菜单版本", "46003" => "不存在的菜单数据", "46004" => "不存在的用户", "47001" => "解析JSON/XML内容错误", "48001" => "api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限", "50001" => "用户未授权该api", "61451" => "参数错误", "61452" => "无效客服账号", "61453" => "客服帐号已存在", "61454" => "客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)", "61455" => "客服帐号名包含非法字符(仅允许英文+数字)", "61456" => "客服帐号个数超过限制(10个客服账号)", "61457" => "无效头像文件类型", 
	"61450" => "系统错误", 
	"61500" => "日期格式错误", 
	"61501" => "日期范围错误", );

	protected $appid = "";
	protected $appsecret = "";

	function __construct($appid, $appsecret) {
		$this -> appid = $appid;
		$this -> appsecret = $appsecret;
	}

	//=================微信接口方法

	/**
	 * 获取accesstoken,缓存7000秒，缓存key="WEIXIN_"$appid$secret
	 * @param $appid 公众号appid
	 * @param $scret 公众号appsecret
	 * @return accesstoken
	 * */
	public function getAccessToken() {
		$access_token = S("WEIXIN_" . $this -> appid . $this -> appsecret);
		if ($access_token === false) {
			$url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this -> appid . '&secret=' . $this -> appsecret;
			$json = json_decode($this -> curlGet($url_get));
			$access_token = $json -> access_token;
			//缓存7000秒，公众平台是7200秒
			S("WEIXIN_" . $this -> appid . $this -> appsecret, $access_token, 7000);
		}

		return $access_token;
	}

	/**
	 * 获取永久二维码
	 * @param $accessToken token
	 * @param $scene_str 编码到二维码的字符串 , 字符串类型，长度限制为1到64，仅永久二维码支持此字段
	 * @return $obj
	 */
	public function getQrcode($scene_str) {
		$accessToken = $this -> getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $accessToken;
		$data = array("action_name" => "QR_LIMIT_STR_SCENE", "action_info" => array('scene' => array('scene_str' => $scene_str)));
		$obj = $this -> curlPost($url, json_encode($data));

		//ticket	获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
		//expire_seconds	二维码的有效时间，以秒为单位。最大不超过1800。
		//url  二维码编码的字符串，可以根据此字符串来生成qrcode。
		return $obj;
	}

	//==========================

	public function getSignPackage($url = '') {
		$jsapiTicket = $this -> getJsApiTicket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		if (empty($url)) {
			$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		} else {
			$url = $url;
		}
		$timestamp = time();
		$nonceStr = $this -> createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		//var_dump($string);
		$signature = sha1($string);

		$signPackage = array("appId" => $this ->appid, "nonceStr" => $nonceStr, "timestamp" => $timestamp, "url" => $url, "signature" => $signature, "rawString" => $string);
		return $signPackage;
	}

	/**
	 * jsapi ticket
	 */
	public function getJsApiTicket() {
		$accessToken = $this -> getAccessToken();

		$ticket = S("WEIXIN_TICKET" . $this -> appid . $this -> appsecret);
		if (empty($ticket)) {
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$accessToken."&type=jsapi";
			$res = json_decode($this -> curlGet($url));
			$ticket = $res -> ticket;
			//缓存7000秒，公众平台是7200秒
			S("WEIXIN_TICKET" . $this -> appid . $this -> appsecret, $ticket, 7000);
		}

		return $ticket;

	}
	  private function createNonceStr($length = 16) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	  }

	//===============JS================

	/**
	 * 获取用户列表
	 * @return array(total	关注该公众账号的总用户数,count	拉取的OPENID个数，最大值为10000,data	列表数据，OPENID的列表,next_openid)
	 */
	public function getUserList($nextopenid = '') {
		$accessToken = $this -> getAccessToken();

		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $accessToken;
		if (!empty($nextopenid)) {
			$url = $url . "&next_openid=" . $nextopenid;
		}
		$arr = json_decode($this -> curlGet($url), TRUE);
		return $arr;
	}

	/**
	 * 上传临时素材
	 * @param $filepath 文件路径
	 */
	public function uploadMaterial($filepath, $type = 'image') {

		if (!file_exists($filepath)) {
			return array('status' => false, 'msg' => '文件不存在');
		}

		$accessToken = $this -> getAccessToken();
		$data = array("media" => '@' . $filepath);

		$url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $accessToken . "&type=" . $type;
		$obj = $this -> curlPost($url, $data);
		return $obj;
	}

	/**
	 * 创建自定义菜单
	 * @param $menulist 格式： array(
	 * 			array('type'=>'类型','name'=>'一级菜单名称','key'=>'关联key','url'=>跳转链接,'_child'=>array(
	 * 							array('type'=>'类型','name'=>'二级菜单名称','key'=>'关联key','url'=>跳转链接),
	 * 							array('type'=>'类型','name'=>'二级单名称','key'=>'关联key','url'=>跳转链接),
	 * 					))
	 * 	)
	 */
	public function createMenu($menulist) {

		$accessToken = $this -> getAccessToken();

		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $accessToken;
		$data = array('button' => array());

		foreach ($menulist as $key => $vo) {

			if (isset($vo['_child']) && count($vo['_child']) > 0) {

				$subbutton = array();
				foreach ($vo['_child'] as $key2 => $vo2) {

					if ($vo2['type'] == "click") {
						array_push($subbutton, array('name' => $vo2['name'], 'type' => "click", "key" => $vo2['key']));
					} elseif ($vo2['type'] == "view") {
						array_push($subbutton, array('name' => $vo2['name'], 'type' => "view", "url" => $vo2['url']));
					}

				}

				array_push($data['button'], array("name" => $vo['name'], "sub_button" => $subbutton));

			} else {
				if ($vo['type'] == "click") {
					array_push($data['button'], array("type" => "click", "name" => $vo['name'], "key" => $vo['key']));
				} elseif ($vo['type'] == "view") {
					array_push($data['button'], array("type" => "view", "name" => $vo['name'], "url" => $vo['url']));
				}
			}
		}
		//		dump($menulist);
		//		dump($data);
		$json = json_encode($data, JSON_UNESCAPED_UNICODE);
		$result = $this -> curlPost($url, $json);
		if ($result['status']) {
			if ($result['msg'] -> errcode == 0) {
				return array('status' => true, 'msg' => '');
			} else {
				$index = $result['msg'] -> errcode;

				return array('status' => false, 'msg' => $this -> errcode[$index]);
			}
		} else {
			return array('status' => false, 'msg' => $result['msg']);
		}
	}

	/**
	 * 删除所有菜单
	 */
	public function deleteMenu() {

		$accessToken = $this -> getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=" . $accessToken;
		$obj = json_decode($this -> curlGet($url));
		if ($obj -> errcode == 0) {
			return array('status' => true, 'msg' => '');
		} else {
			return array('status' => false, 'msg' => $this -> errcode[$obj -> errcode]);
		}
	}

	/**
	 * 静默获取用户信息
	 * @param $openid 用户的openid
	 * subscribe	用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
	 * openid	用户的标识，对当前公众号唯一
	 * nickname	用户的昵称
	 * sex	用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
	 * city	用户所在城市
	 * country	用户所在国家
	 * province	用户所在省份
	 * language	用户的语言，简体中文为zh_CN
	 * headimgurl	用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
	 * subscribe_time	用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
	 * unionid	只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
	 */
	public function getBaseUserInfo($openid) {
		/**
		 *
		 */
		$accessToken = $this -> getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $accessToken . "&openid=" . $openid . "&lang=zh_CN";
		$arr = json_decode($this -> curlGet($url), true);
		if (isset($arr['errcode'])) {
			return array('status' => false, 'info' => $this -> errcode[$arr['errcode']]);
		} else {
			return array('status' => true, 'info' => $arr);
		}
	}

	/**
	 * 获取scope=snsapi_base 的跳转链接
	 * @param $redirectURL 跳转链接
	 */
	public function getOAuth2BaseURL($redirectURL, $state = '_openid_', $scope = 'snsapi_base') {
		$redirectURL = urlencode($redirectURL);
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirectURL&response_type=code&scope=$scope&state=$state#wechat_redirect";
		return $url;
	}

	/**
	 * {
	 "access_token":"ACCESS_TOKEN",
	 "expires_in":7200,
	 "refresh_token":"REFRESH_TOKEN",
	 "openid":"OPENID",
	 "scope":"SCOPE"
	 }
	 */
	public function getOAuth2AccessToken($code) {
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this -> appid . "&secret=" . $this -> appsecret . "&code=" . $code . "&grant_type=authorization_code";
		$arr = json_decode($this -> curlGet($url), true);
		return $arr;
	}

	/**
	 * 主动发送文本消息给粉丝
	 * 走客服接口
	 */
	public function sendTextToFans($openid, $text) {

		$accessToken = $this -> getAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$accessToken";
		$textData = array('touser' => $openid, 'msgtype' => 'text', 'text' => array('content' => $text));

		$json = json_encode($textData, JSON_UNESCAPED_UNICODE);
		$result = $this -> curlPost($url, $json);

		if ($result['status']) {
			if ($result['msg'] -> errcode == 0) {
				return array('status' => true, 'msg' => 'success');
			} else {
				$index = $result['msg'] -> errcode;

				return array('status' => false, 'msg' => $this -> errcode[$index]);
			}
		} else {
			return array('status' => false, 'msg' => $result['msg']);
		}

	}

	/**
	 *TODO: 主动发送模板消息给粉丝
	 * 走模板消息接口
	 */
	public function sendTmplMsgToFans() {

	}

	//===============================================================

	protected function curlPost($url, $data) {
		
		$ch = curl_init();
//		$header = "Accept-Charset: utf-8";
		$header = array('Accept-Charset'=>"utf-8");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$tmpInfo = curl_exec($ch);
		$errorno = curl_errno($ch);
		if ($errorno) {
			return array('status' => false, 'msg' => $errorno);
		} else {
			$js = json_decode($tmpInfo);
			return array('status' => true, 'msg' => $js);
		}
	}

	protected function curlGet($url) {
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
	}

}
