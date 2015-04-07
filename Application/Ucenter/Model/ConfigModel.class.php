<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Model;

use Think\Model;

class ConfigModel extends Model {

	protected $tablePrefix = 'common_';

	protected $_validate = array(
	//array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	//验证规则：require 字段必须、email 邮箱、url URL地址、currency 货币、number 数字
	//doclink: http://document.thinkphp.cn/manual_3_2.html#auto_validate
	array('title', 'require', '{%MV_CONFIG_TITLE}'), array('name', 'require', '{%MV_CONFIG_NAME}'), );

	protected $_auto = array(
	//array(完成字段1,完成规则,[完成条件,附加规则]),
	array('status', '1', self::MODEL_INSERT), array('create_time', 'time', self::MODEL_INSERT, 'function'), array('update_time', 'time', self::MODEL_BOTH, 'function'));

	/**
	 * 设置 
	 * @return true 设置成功 false 参数不正确
	 */
	public function set($config) {
		$effects = 0;
		if ($config && is_array($config)) {
			
			foreach ($config as $name => $value) {
				$map = array('name' => $name);
				$result = $this -> where($map) -> setField('value', $value);
				if($result !== false){
					$effects = $effects + $result;
				}
			}
			if($effects === 0){
				return false;
			}
			return $effects;
		}
		return false;
	}

}
