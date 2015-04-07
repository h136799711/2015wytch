<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Weixin\Controller;
use Think\Controller;

class WeixinController extends Controller {
	
	protected function _initialize() {
				
		// 获取配置
		$this -> getConfig();
		

		if (!defined('APP_VERSION')) {
			//定义版本
			if (defined("APP_DEBUG") && APP_DEBUG) {
				define("APP_VERSION", time());
			} else {
				define("APP_VERSION", C('APP_VERSION'));
			}
		}
		
	}
	
	
	/**
	 * 从数据库中取得配置信息
	 */
	protected function getConfig() {
		$config = S('weixin_config' );
		
		if ($config === false) {
			$map = array();
			$fields = 'type,name,value';
			$result = apiCall('Admin/Config/queryNoPaging', array($map, false, $fields));
			if ($result['status']) {
				$config = array();
				if (is_array($result['info'])) {
					foreach ($result['info'] as $value) {
						$config[$value['name']] = $this -> parse($value['type'], $value['value']);
					}
				}
				//缓存配置300秒
				S("weixin_config" , $config, 300);
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		}
		C($config);
	}
	
	/**
	 * 根据配置类型解析配置
	 * @param  integer $type  配置类型
	 * @param  string  $value 配置值
	 */
	private static function parse($type, $value) {
		switch ($type) {
			case 3 :
				//解析数组
				$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
				if (strpos($value, ':')) {
					$value = array();
					foreach ($array as $val) {
						list($k, $v) = explode(':', $val);
						$value[$k] = $v;
					}
				} else {
					$value = $array;
				}
				break;
		}
		return $value;
	}
	
}