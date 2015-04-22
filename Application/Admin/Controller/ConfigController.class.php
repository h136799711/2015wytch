<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class ConfigController extends AdminController {

	protected function _initialize() {
		parent::_initialize();
		$this -> assignTitle(L('C_CONFIG'));
	}
	
	/**
	 * 配置
	 */
	public function index() {

		$map = array();
		$map['name'] = array('like', '%' . I('name', '') . '%');
		if(I('group',-1) !== -1){
			$map['group'] = I('group');
		}
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = 'update_time desc';
		$result = apiCall('Admin/Config/query', array($map, $page, $order));
		if ($result['status']) {
			$this -> assign("config_groups", C('CONFIG_GROUP_LIST'));
			$this -> assign("show", $result['info']['show']);
			$this -> assign("list", $result['info']['list']);
			$this -> display();
		} else {
			$this -> error(L('ERR_SYSTEM_BUSY'));
		}
	}
	
	/**
	 * 设置
	 */
	public function set(){
		if(IS_GET){
			$this->configVars();
			$this->display();	
		}else{
			$config = I('config');
			$order = 'sort desc';
 			$result = apiCall("Admin/Config/set",array($config,$order));
			if($result['status']){
				//清除缓存
        		S("config_" . session_id() . '_' . session("uid"),null);
        		$this->success(L('RESULT_SUCCESS'),U('Admin/Config/set'));
			}else{
				LogRecord($result['info'], '[INFO]'.__FILE.__LINE__);
				$this -> error($result['info']);
			}
		}
	}
	
		/**
	 * 更新保存，根据主键默认id
	 * 示列url:
	 * /Admin/Menu/save/id/33
	 * id必须以get方式传入
	 */
	public function save(){
		parent::save();	
	}

	/**
	 * 添加
	 */
	public function add() {
		if (IS_GET) {
			$this->configVars();
			$this -> display();
		} else {
			$menu = I('post.');
			parent::addTo($menu, U('Admin/Config/index'));
		}
	}
	
	public function edit() {
		$this->configVars();
		parent::edit();
	}
	
	
	
	
	/**
	 * 配置分组与类型参数
	 */
	protected function configVars() {
		//配置分组
		$config_groups = C('CONFIG_GROUP_LIST');
		//配置类型
		$config_types = C('CONFIG_TYPE_LIST');
		
		$this -> assign('config_groups', $config_groups);
		$this -> assign('config_types', $config_types);
	}

}
