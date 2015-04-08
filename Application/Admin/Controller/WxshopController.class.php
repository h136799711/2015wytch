<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Controller;

class WxshopController extends AdminController{
	
	protected function _initialize(){
		parent::_initialize();
	}
	public function index(){
		$this->display();
	}
	/**
	 * 商品预创建－选择类目
	 */
	public function precreate(){
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		
		if(IS_POST){
			//保存
		}else{
			
			$result = $wxshopapi->category(1);
			
			if($result['status']){
				$this->assign("rootcate",$result['msg']);
			}
			$this->display();
		}
	}
	
	/**
	 * 添加商品
	 */
	public function create(){
		if(IS_POST){
			
		}else{
			$catename = I('catename','');
			$this->assign("catename",$catename);
			$this->assign("cates",I('cates',''));
			$this->display();
		}
	}
	
	/**
	 * 分组信息
	 */
	public function groups(){
		
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		$result = $wxshopapi->groupGetAll();	
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
	}
	
	//分组获取ajax
	public function cate(){
		$cate_id = I('cateid',-1);
		if($cate_id == -1){
			$this->success(array());
		}
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		
			
		$result = $wxshopapi->category($cate_id);
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
	}
	
}
