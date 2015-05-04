<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Service;

abstract class Service {
	
	
	/**
	 * Service调用模型实例
	 * @access  protected
	 * @var object
	 */
	protected $model;

	/**
	 * 构造方法，检测相关配置
	 */
	public function __construct() {
		$this -> _initialize();
	}

	/**
	 * 抽象方法，用于设置模型实例
	 */
	abstract protected function _initialize();
	
	
	/**
	 * 返回错误结构
	 * @return array('status'=>boolean,'info'=>Object)
	 */
	protected function returnErr($info) {
		return array('status' => false, 'info' => $info);
	}

	/**
	 * 返回成功结构
	 * @return array('status'=>boolean,'info'=>Object)
	 */
	protected function returnSuc($info) {
		return array('status' => true, 'info' => $info);
	}

	
}
