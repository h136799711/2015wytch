<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class AuthRuleController extends AdminController {

	protected function _initialize() {
		parent::_initialize();
	}

	public function add($title,$url,$type) {
		
		if(trim($url) === "#"){
			//不作为权限节点
			return true;
		}
		//
		if (substr_count('/', $url) == 2) {
			$url = 'Admin/' . $url;
		}
		
		$entity = array('module' => 'Admin', 'title' => $title, 'name' => $url, 'status' => 1, 'condition' => '','type'=>$type);
		
		$result = apiCall("Admin/AuthRule/add", array($entity));

		if (!$result['status']) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			return false;
		}else{
			return true;
		}
	}
	
	public function delete($title,$url,$type){
		if (substr_count('/', $url) == 2) {
			$url = 'Admin/' . $url;
		}
		
		$map = array('module' => 'Admin', 'title' => $title, 'name' => $url, 'status' => 1,'type'=>$type);
		
		$result = apiCall("Admin/AuthRule/pretendDelete", array($map));

		if (!$result['status']) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			return false;
		}else{
			return true;
		}
		
	}
	
	/**
	 * 更新保存
	 */
	public function save($title,$url,$type,$newEntity){
		if (substr_count('/', $url) == 2) {
			$url = 'Admin/' . $url;
		}
		
		$map = array('module' => 'Admin', 'title' => $title, 'name' => $url, 'status' => 1,'type'=>$type);
		
		$result = apiCall("Admin/AuthRule/save", array($map,$newEntity));

		if (!$result['status']) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			return false;
		}else{
			return true;
		}
		
	}

}
