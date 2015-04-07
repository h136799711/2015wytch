<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Model;
use Think\Model;

/**
 * 统一用户管理员表
 */
class UcenterAdminModel extends Model {
	
	/**
	 * 前缀
	 */
	protected $tablePrefix = "uc_";
	
	/**
	 * 登录
	 * @return 用户对象 -2 密码错误 -1 用户不存在或被禁用
	 */
	public function login($username, $password) {
		$map = array('username' => $username);
		$user = D('UcenterMember') -> where($map) -> find();
		if (is_array($user) && $user['status'] == 1) {
			/* 验证用户密码 */
			if (think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
				$this -> updateLogin($user['id']);
				//更新用户登录信息
				$uid = $user['id'];
				//登录成功，返回用户ID
			} else {
				$uid = -2;
				//密码错误
			}
		} else {
			$uid = -1;
			//用户不存在或被禁用
		}
		
		if ($uid > 0) {
			
			$admin = $this -> where(array('member_id' => $uid)) -> find();
			
			if (is_array($admin) && $admin['status'] == 1) {
				return $admin;
			} else {
				$uid = -1;
			//用户不存在或被禁用
			}

		}
		
		return $uid;
	}

	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid) {
		$data = array('id' => $uid, 'last_login_time' => NOW_TIME, 'last_login_ip' => get_client_ip(1), );
		M('UcenterMember') -> save($data);
	}
	
	/**
	 * 获取用户信息
	 * return 错误：false 用户不存在或被禁用；成功：$user id,username,email,mobile,last_login_time,last_login_ip
	 */
	public function getUserinfo($uid,$is_username=false){
		$map = array();
		if($is_username){ //通过用户名获取
			$map['username'] = $uid;
		} else {
			$map['id'] = $uid;
		}
		
		$user = M('UcenterMember')->where($map)->field('id,username,email,mobile,status,last_login_time,last_login_ip')->find();
		if(is_array($user)){
			return $user;
		} else {
			return false; //用户不存在或被禁用
		}
	}
	
	

}
