<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class AuthGroupAccessController extends AdminController{
	
	protected function _initialize() {
		parent::_initialize();
	}
	
//	public function test(){
//		dump(intval("aaddassd"));
//	}
	
//	public function addTest(){
//		$uid = 34;
//		$groupid = array(12,13,15);
//		$result = apiCall("Admin/AuthGroupAccess/addToGroup",array($uid,$groupid));
//		dump($result);
//	}

	
	/**
	 * 将指定用户添加到指定用户组
	 */
	public function addToGroup(){
		
		$uid = I('post.uid','');
		$groupid = I('post.groupid','');
		
		if(empty($uid) || empty($groupid)){
			$this->error("参数错误");
		}
		
		if(is_administrator($uid)){
			$this->error("不能对超级管理员进行操作");
		}
		
		if($groupid){
			$groupid = intval($groupid);
		}
		
		$result = apiCall("Admin/AuthGroupAccess/addToGroup",array($uid,$groupid));
		
		if($result['status']){			
			$this->success("操作成功~",U('Admin/AuthManage/user',array('groupid'=>$groupid)));
		}else{
			LogRecord($result['info'], __FILE__.__LINE__);
			$this->error($result['info']);
		}
	}

	/**
	 * 将指定用户从指定用户组移除
	 */
	public function delFromGroup(){
		$groupid = I('groupid',-1);
		$uid = I('uid',-1);
		if($groupid === -1 || $uid === -1){
			$this->error("参数错误！");
		}
		$map = array('uid'=>$uid,"group_id"=>$groupid);
		
		$result = apiCall("Admin/AuthGroupAccess/delete",array($map));
		if($result['status']){
			$this->success("操作成功~",U('Admin/AuthManage/user'));
		}else{
			LogRecord($result['info'], __FILE__.__LINE__);
			$this->error($result['info']);
		}
		
	}
}
