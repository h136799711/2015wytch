<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Ucenter\Controller;

class AccountController extends UcenterController{
	
	public function updatepassword(){
		$this->display();
	}
	
	/**
	 * 提交更新密码
	 */
	public function submitpassword(){
		if(IS_GET){
			return ;
		}
		  //获取参数
        $password   =   I('post.old');
        empty($password) && $this->error(L('ERR_NEED_OLD_PASSWORD'));
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error(L('ERR_NEED_NEW_PASSWORD'));
        $repassword = I('post.repassword');
        empty($repassword) && $this->error(L('ERR_NEED_CONFIRM_PASSWORD'));
		
        if($data['password'] !== $repassword){
            $this->error(L('ERR_NEED_SAME_PASSWORD'));
        }
		        
        $res    = apiCall('Uclient/User/updateInfo',array(UID, $password, $data));
		
        if($res['status']){
            $this->success(L('RESULT_SUCCESS'));
        }else{
            $this->error($res['info']);
        }
	}
	
	
}
