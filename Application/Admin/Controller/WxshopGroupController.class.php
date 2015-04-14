<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class WxshopGroupController extends AdminController{
	
	
	public function index(){
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		
		if(IS_POST){
			//保存
		}else{
			
			$result = $wxshopapi->groupGetAll();
			
			if($result['status']){
				$this->assign("groups",$result['info']);
			}
			$this->display();
		}
	}
	
	public function del(){
		if(IS_GET){
			$group_id = I('group_id','');
			
			$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
			$result = $wxshopapi->groupDel($group_id);	
			if($result['status']){
				$this->success("删除成功!");
			}else{
				$this->error($result['info']);
			}
			
		}
	}
	
	public function add(){
		
		$groupName = I('group_name','');
		
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		$result = $wxshopapi->groupAdd($groupName,array());	
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
	}
	
	public function edit(){
		
		$group_id = I('group_id','');
		$group_name = I('group_name','');
		
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		$result = $wxshopapi->groupModify($group_id,$group_name);	
		if($result['status']){
			$this->success("保存成功!");
		}else{
			$this->error($result['info']);
		}
	}
	
	/**
	 * 商品管理
	 * 
	 */
	public function product(){
		$group_id = I('group_id','');
		
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		$result = $wxshopapi->groupGetByID($group_id);	
		if($result['status']){
			$this->assign("list",$result['info']['product_list']);
			$this->display();
		}else{
			$this->error($result['info']);
		}
	}
	
	
	/**
	 * 分组信息
	 */
	public function groups(){
		if(IS_POST){
			$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
			$result = $wxshopapi->groupGetAll();	
			if($result['status']){
				$this->success($result['info']);
			}else{
				$this->error($result['info']);
			}
		}
	}
}
