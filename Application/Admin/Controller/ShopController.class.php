<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Admin\Controller;

class ShopController extends AdminController{
	
	/**
	 * 商城配置
	 */
	public function config(){
		if(IS_GET){
			$map = array('name'=>"WXPAY_OPENID");
			$result = apiCall("Admin/Config/getInfo", array($map));
			if($result['status']){
				$this->assign("wxpayopenid",	$result['info']['value']);
				$this->display();
			}
		}elseif(IS_POST){
			
			$openids = I('post.openids','');
			
			$config = array("WXPAY_OPENID"=>$openids);
			$result = apiCall("Admin/Config/set", array($config));
			if($result['status']){
				C('WXPAY_OPENID',$openids);
				
				$this->success(L('RESULT_SUCCESS'),U('Shop/config'));
			}else{
				if(is_null($result['info'])){
					$this->error("无更新！");
				}else{
					$this->error($result['info']);
				}
			}
			
		}
	}
	
}
