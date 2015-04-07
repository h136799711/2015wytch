<?php

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter') {
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 获取链接
 * 传入U方法可接受的参数或以http开头的完整链接地址
 * @return 链接地址
 */
function getURL($str, $param = '') {
	if (trim($str) == '#') {
		return '#';
	}
	if (strpos($str, '?') === false) {
		$str = $str . '?' . $param;
	} else {
		$str = $str . '&' . $param;
	}
	if (strpos($str, "http") === 0) {
		return $str;
	}
	
	return U($str);
}

/**
 * 判断链接是否激活
 * 根据session来检测
 * @return ''|'active'
 */
function isActiveMenuURL($id) {
	$activemenuid = session('activemenuid');
	if ($activemenuid === $id) {
		return 'active';
	}
	return '';
}

/**
 * 判断链接是否激活
 * 根据session来检测
 * @return ''|'active'
 */
function isActiveSubMenuURL($id) {
	$activesubmenuid = session('activesubmenuid');
	if ($activesubmenuid === $id) {
		return 'active';
	}
	return '';
}


// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
	$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
	if (strpos($string, ':')) {
		$value = array();
		foreach ($array as $val) {
			list($k, $v) = explode(':', $val);
			$value[$k] = $v;
		}
	} else {
		$value = $array;
	}
	return $value;
}
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
/* 判断当前时间是否为晚上  */
function isNight(){
	$hour=date('G',time());
	if($hour > 18 || $hour < 6){
		return true;
	}else{
		return false;
	}
}
