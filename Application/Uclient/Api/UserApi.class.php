<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Uclient\Api;
use Uclient\Api\Api;
use Uclient\Model\UcenterMemberModel;

class UserApi extends Api{
    /**
     * 构造方法，实例化操作模型
     */
    protected function _init(){
        $this->model = new UcenterMemberModel();
    }
	
    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return array(true,uid)  注册失败-错误信息
     */
    public function register($username, $password, $email, $mobile = ''){
        $result = $this->model->register($username, $password, $email, $mobile);
    	if($result > 0){//成功
    		return array('status'=>true,'info'=>$result);
    	}else{
    		return array('status'=>false,'info'=>$this->getRegisterError($result));
    	}
	}

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1){
    	
        $result = $this->model->login($username, $password, $type);	
		if($result > 0){
    		return array('status'=>true,'info'=>$result);
		}else{
			switch($result){
				case 0:
					$result = "参数错误";
					break;
				case -1:
					$result = "用户不存在或被禁用";
					break;
				case -2:
					$result = "密码错误";
					break;
				default:
					$result = "未知";
					break;
			}
    		return array('status'=>false,'info'=>$result);
		}
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false){
        $result = $this->model->info($uid, $is_username);
		if($result === -1){
			return array('status'=>false,'info'=>'用户不存在');
		}else{
			return array('status'=>true,'info'=>$result);
		}
    }

    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username){
        $result =  $this->model->checkField($username, 1);
    	if($result > 0){
    		return array('status'=>true,'info'=>$result);
    	}else{
    		return array('status'=>false,'info'=>$result);    		
    	}
	}

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email){
        $result = $this->model->checkField($email, 2);
		if($result > 0){
    		return array('status'=>true,'info'=>$result);
    	}else{
    		return array('status'=>false,'info'=>$result);    		
    	}
    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile){
        $result = $this->model->checkField($mobile, 3);
		if($result > 0){
    		return array('status'=>true,'info'=>$result);
    	}else{
    		return array('status'=>false,'info'=>$result);    		
    	}
    }
	
	/**
	 * 更新密码
	 * @param integer @uid 用户id
	 * @return array(status,info)
	 */
	public function updatePwd($uid,$password){
        if($this->model->updatePwd($uid, $password) !== false){
		
			$return['status'] = true;
        
		}else{
            $return['status'] = false;
            $return['info'] = $this->model->getDbError();
        }
		
        return $return;
		
	}
    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateInfo($uid, $password, $data){
        if($this->model->updateUserFields($uid, $password, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;			
            $return['info'] = $this->getRegisterError($this->model->getError());
        }
        return $return;
    }
	
	/**
	 * 获取注册错误代码的描述信息
	 * 
	 */
	public function getRegisterError($error){
		$errDesc = "";
		switch ($error) {
			case -1:
				$errDesc = "用户名长度不合法";
				break;
			case -2:
				$errDesc = "用户名禁止注册";
				break;
			case -3:			
				$errDesc = "用户名被占用";	
				break;
			case -4:
				$errDesc = "密码长度不合法";
				break;
			case -5:
				$errDesc = "邮箱格式不正确";
				break;
			case -6:
				$errDesc = "邮箱长度不合法";
				break;
			case -7:
				$errDesc = "邮箱禁止注册";
				break;
			case -8:
				$errDesc = "邮箱被占用";
				break;
			case -9:
				$errDesc = "手机格式不正确";
				break;
			case -10:
				$errDesc = "手机禁止注册";				
				break;
			case -11:
				$errDesc = "手机号被占用";
				break;
			
			default:
				$errDesc = '未知原因';
				break;
		}
		
		return $errDesc;
	}
	

}
