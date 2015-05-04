<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
	$user = session('global_user');
	if (empty($user)) {
		return 0;
	} else {
		return session('global_user_sign') == data_auth_sign($user) ? session('uid') : 0;
	}
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null) {
	$uid = is_null($uid) ? is_login() : $uid;
	return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * apiCall
 */
function apiCall($url, $vars, $layer = 'Api') {
	//TODO:考虑使用func_get_args 获取参数数组
	return R($url, $vars, $layer);
}
/**
 * ServiceCall
 */
function serviceCall($url, $vars) {
	//TODO:考虑使用func_get_args 获取参数数组
	return R($url, $vars, 'Service');
}

/**
 * 记录日志，系统运行过程中可能产生的日志
 * Level取值如下：
 * EMERG 严重错误，导致系统崩溃无法使用
 * ALERT 警戒性错误， 必须被立即修改的错误
 * CRIT 临界值错误， 超过临界值的错误
 * WARN 警告性错误， 需要发出警告的错误
 * ERR 一般性错误
 * NOTICE 通知，程序可以运行但是还不够完美的错误
 * INFO 信息，程序输出信息
 * DEBUG 调试，用于调试信息
 * SQL SQL语句，该级别只在调试模式开启时有效
 */
function LogRecord($msg, $location, $level = 'ERR') {
	Think\Log::write($location . $msg, $level);
}

/**
 * 如果操作失败则记录日志
 * @return array 格式：array('status'=>boolean,'info'=>'错误信息')
 * @author hebiduhebi@163.com
 */
function ifFailedLogRecord($result, $location) {
	if ($result['status'] === false) {
		Think\Log::write($location . $result['info'], 'ERR');
	}
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
	//数据类型检测
	if (!is_array($data)) {
		$data = (array)$data;
	}
	ksort($data);
	//排序
	$code = http_build_query($data);
	//url编码并生成query字符串
	$sign = sha1($code);
	//生成签名
	return $sign;
}

/**
 * 获取一个日期时间段
 * 如果有查询参数包含startdatetime，enddatetime，则优先使用否则生成
 * @param $type 0|1|2|3｜其它
 * @return array("0"=>开始日期,"1"=>结束日期)
 */
function getDataRange($type) {
	$result = array();
	switch($type) {
		case 0 :
			//今天之内
			$result['0'] = I('startdatetime', (date('Y-m-d 00:00:00', time())), 'urldecode');
			break;
		case 1 :
			//昨天
			$result['0'] = I('startdatetime', (date('Y-m-d 00:00:00', time() - 24 * 3600)), 'urldecode');
			$result['1'] = I('enddatetime', (date('Y-m-d 00:00:00', time())), 'urldecode');
			break;
		case 2 :
			//最近7天
			$result['0'] = I('startdatetime', (date('Y-m-d H:i:s', time() - 24 * 3600 * 7)), 'urldecode');
			break;
		case 3 :
			//最近30天
			$result['0'] = I('startdatetime', (date('Y-m-d H:i:s', time() - 24 * 3600 * 30)), 'urldecode');
			break;
		default :
			$result['0'] = I('startdatetime', (date('Y-m-d 00:00:00', time() - 24 * 3600)), 'urldecode');
			break;
	}
	if (!isset($result['1'])) {
		$result['1'] = I('enddatetime', (date('Y-m-d H:i:s', time() + 10)), 'urldecode');
	}
	return $result;
}

/**
 * 返回 是|否
 * @param $param 一个值|对象等
 * @return 空|false|0 时返回否，否则返回是
 */
function yesorno($param) {
	if (is_null($param) || $param === false || $param == 0 || $param == "0") {
		return L("NO");
	} else {
		return L('YES');
	}
}

/**
 * 返回数据状态的含义
 * @status $status 一个数字 -1,0,1,2,3 其它值都是未知
 * @return 描述字符串
 */
function getStatus($status) {
	$desc = '未知状态';
	switch($status) {
		case -1 :
			$desc = "已删除";
			break;
		case 0 :
			$desc = "禁用";
			break;
		case 1 :
			$desc = "正常";
			break;
		case 2 :
			$desc = "待审核";
			break;
		case 3 :
			$desc = "通过";
			break;
		case 4 :
			$desc = "不通过";
			break;
		default :
			break;
	}
	return $desc;
}

/**
 * 获得皮肤的字符串表示
 */
function getSkin($skin) {
	$desc = '';

	switch($skin) {
		case 0 :
			$desc = "simplex";
			break;
		case 1 :
			$desc = "flatly";
			break;
		case 2 :
			$desc = "darkly";
			break;
		case 3 :
			$desc = "cosmo";
			break;
		default :
			$desc = "simplex";
			break;
	}
	return $desc;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = &$list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] = &$list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent = &$refer[$parentId];
					$parent[$child][] = &$list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array()) {
	if (is_array($tree)) {
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if (isset($reffer[$child])) {
				unset($reffer[$child]);
				tree_to_list($value[$child], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby = 'asc');
	}
	return $list;
}

/**
 * 获取图片表的图片链接
 */
function getPictureURL($localpath, $remoteurl) {
	if (strpos($remoteurl, "http") === 0) {
		return $remoteurl;
	}
	return __ROOT__ . $localpath;
}

function GUID() {
	if (function_exists('com_create_guid') === true) {
		return trim(com_create_guid(), '{}');
	}

	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function addWeixinLog($data, $operator = '') {
	$log['ctime'] = time();
	$log['loginfo'] = is_array($data) ? serialize($data) : $data;
	$log['operator'] = $operator;
	$weixinlog = new \Common\Model\WeixinLogModel();
	$weixinlog -> add($log);
}

/**
 * 获取订单状态的文字描述
 */
function getOrderStatus($status) {

	switch($status) {
		case \Common\Model\OrdersModel::ORDER_COMPLETED :
			return "已完成";
		case \Common\Model\OrdersModel::ORDER_RETURNED :
			return "已退货";
		case \Common\Model\OrdersModel::ORDER_SHIPPED :
			return "已发货";
		case \Common\Model\OrdersModel::ORDER_TOBE_CONFIRMED :
			return "待确认";
		case \Common\Model\OrdersModel::ORDER_TOBE_SHIPPED :
			return "待发货";
		case \Common\Model\OrdersModel::ORDER_CANCEL :
			return "订单已关闭";
		case \Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS :
			return "已收货";
		case \Common\Model\OrdersModel::ORDER_BACK :
			return "卖家退回";
		default :
			return "未知";
	}
}

/**
 * 获取支付状态的文字描述
 */
function getPayStatus($status) {
	switch($status) {
		case \Common\Model\OrdersModel::ORDER_PAID :
			return "已支付";
		case \Common\Model\OrdersModel::ORDER_TOBE_PAID :
			return "待支付";
		case \Common\Model\OrdersModel::ORDER_REFUND :
			return "已退款";
		case \Common\Model\OrdersModel::ORDER_CASH_ON_DELIVERY :
			return "货到付款";
			
		default :
			return "未知";
	}
}

/**
 * 获取数据字典的ID
 * TODO: 考虑从数据库中获取
 */
function getDatatree($code) {
	return C("DATATREE." . $code);
}

/**
 * 使用fsockopen请求地址
 * @param $url 请求地址 ，完整的地址，
 * @param $post_data 请求参数，数组形式
 * @param $cookie
 * @param $repeat TODO: 重复次数
 */
function fsockopenRequest($url, $post_data = array(), $cookie = array(), $repeat = 1) {
	$method = "GET";
	//通过POST或者GET传递一些参数给要触发的脚本
	$url_array = parse_url($url);
	//获取URL信息
	$port = isset($url_array['port']) ? $url_array['port'] : 80;
	//5秒超时
	$fp = @fsockopen($url_array['host'], $port, $errno, $errstr, 5);
	if (!$fp) {
		//连接失败
		return FALSE;
	}
	//非阻塞设置
	stream_set_blocking($fp, FALSE);
	$getPath = $url_array['path'] . "?" . $url_array['query'];
	if (!empty($post_data)) {
		$method = "POST";
	}
	$header = $method . " " . $getPath;
	$header .= " HTTP/1.1\r\n";
	$header .= "Host: " . $url_array['host'] . "\r\n";
	//HTTP 1.1 Host域不能省略
	/*以下头信息域可以省略 */

	$header .= "Referer:http://" . $url_array['host'] . " \r\n";
	//		$header .= "User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X; en-us) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53 \r\n";
	//		$header .= "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8 \r\n";
	//		$header .= "Accept-Language:zh-CN,zh;q=0.8,en;q=0.6 \r\n";
	//		$header .= "Accept-Encoding:gzip, deflate, sdch \r\n";

	$header .= "Connection:Close\r\n";
	//		$header .= "Keep-Alive: 3\r\n";
	//		$header .= "Connection: keep-alive\r\n";
	//需要重复2次
	if (!empty($cookie)) {
		$_cookie = strval(NULL);
		foreach ($cookie as $k => $v) {
			$_cookie .= $k . "=" . $v . "; ";
		}
		$cookie_str = "Cookie: " . base64_encode($_cookie) . " \r\n";
		//传递Cookie
		$header .= $cookie_str;
	}
	if (!empty($post_data)) {
		$_post = strval(NULL);
		$i == 0;
		foreach ($post_data as $k => $v) {
			if ($i == 0) {
				$_post .= $k . "=" . $v;
			} else {
				$_post .= '&'.$k . "=" . urlencode($v);
			}
			$i++;
		}

		//			$post_str = "Content-Type: multipart/form-data; charset=UTF-8	\r\n";
		$post_str = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8	\r\n";
		//			$_post = "username=demo&password=hahaha";
		$post_str .= "Content-Length: " . strlen($_post) . "\r\n";
		$post_str .= "\r\n";
		//POST数据的长度
		$post_str .= $_post;
		//传递POST数据
		$header .= $post_str;
	}

	fwrite($fp, $header);
	//TODO: 从返回结果来判断是否成功
	//		$result = "";
	//		while(!feof($fp)){//测试文件指针是否到了文件结束的位置
	//		   $result.= fgets($fp,128);
	//		}

	//		$result = split("\r\n", $result);
	//		for($i=count($result)-1;$i>=0;$i--){
	//			dump($result);
	//		}

	fclose($fp);
	return true;
}
/**
 * 获取当前完整url
 */
function getCurrentURL(){
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	return $url;
}

