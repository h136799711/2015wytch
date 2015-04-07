<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Api;
use Common\Api\Api;
use Ucenter\Model\UcenterAdminModel;


class UcenterAdminApi extends  Api{
	
	protected function _init(){
		$this->model = new UcenterAdminModel();
	}
	
	/**
	 * 服务端登录
	 * 仅限总管理员登录
	 * @return array
	 * TODO: 考虑应用管理员登录
	 * */
	public function login($username,$password){
		$result =  $this->model->login($username,$password);
		
		if(is_array($result)){
			return $this->apiReturnSuc($result);
		}else{
			return $this->apiReturnErr($this->getLoginError($result));
		}
	}
	
	/**
	 * 获取用户信息
	 * id,username,email,mobile,last_login_time,last_login_ip,status
	 */
	public function getUserinfo($uid,$is_username=false){
		$result = $this->model->getUserinfo($uid,$is_username);
		if($result === false){
			return $this->apiReturnErr("用户不存在");
		}else{
			return $this->apiReturnSuc($result);
		}
	}
	
	/**
	 * 获取登录错误信息
	 */
	public function getLoginError($result){
		
		$errDesc = '未知错误';
		switch($result){
			case -1:
				$errDesc = "用户不存在或被禁用";
				break;
			case -2:
				$errDesc = "密码错误";
				break;
			default:
				
				break;
		}
		
		return $errDesc;
	}
	
}