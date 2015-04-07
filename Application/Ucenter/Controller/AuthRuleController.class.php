<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Controller;

class AuthRuleController extends UcenterController {

	protected function _initialize() {
		parent::_initialize();
	}

	/**
	 * Index 
	 */
	public function index(){
		
		$module = I('module');
		$title = I('title');
		$url = I('url');
		$url = I('type');
		
		$map = array('module' => $module, 'title' => $title, 'name' => $url, 'status' => 1, 'condition' => '','type'=>$type);
		
		$result = apiCall("Ucenter/AuthRule/query", array($map));
		if($result['status']){
			
			$this->display();
		}else{
			echo "未知错误！";
		}
	}
	
	public function add($title,$url,$type) {
		
		if(trim($url) === "#"){
			//不作为权限节点
			return true;
		}
		//
		if (substr_count('/', $url) == 2) {
			$url = 'Ucenter/' . $url;
		}
		
		$entity = array('module' => 'ucenter', 'title' => $title, 'name' => $url, 'status' => 1, 'condition' => '','type'=>$type);
		
		$result = apiCall("Ucenter/AuthRule/add", array($entity));

		if (!$result['status']) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			return false;
		}else{
			return true;
		}
	}
	
	public function delete($title,$url,$type){
		if (substr_count('/', $url) == 2) {
			$url = 'Ucenter/' . $url;
		}
		
		$map = array('module' => 'ucenter', 'title' => $title, 'name' => $url, 'status' => 1,'type'=>$type);
		
		$result = apiCall("Ucenter/AuthRule/pretendDelete", array($map));

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
			$url = 'Ucenter/' . $url;
		}
		
		$map = array('module' => 'ucenter', 'title' => $title, 'name' => $url, 'status' => 1,'type'=>$type);
		
		$result = apiCall("Ucenter/AuthRule/save", array($map,$newEntity));

		if (!$result['status']) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			return false;
		}else{
			return true;
		}
		
	}

}
