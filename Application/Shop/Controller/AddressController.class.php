<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

class AddressController extends ShopController{
	
	/**
	 * 收货地址选择
	 */
	public function choose(){
		$map = array();
		$map['wxuserid'] = $this->userinfo['id'];
		
		$result = apiCall("Shop/Address/query", array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		
		$this->assign("list",$result['info']['list']);
		
		$this->display();
		
	}
	
	public function add(){
		if(IS_GET){
			$map = array();
			$map['wxuserid'] = $this->userinfo['id'];
			$map['default'] = 1;
			
			$result = apiCall("Shop/Address/getInfo", array($map));

			if(!$result['status']){
				$this->error($result['info']);
			}
			if(is_array($result['info'])){
				//获取城市，区域信息
				$city  = apiCall("Tool/City/getListByProvinceID", array($result['info']['province']));
				if(!$city['status']){
					LogRecord($city['info'], __FILE__.__LINE__);
				}
				$this->assign("city",$city['info']);
				$area  = apiCall("Tool/Area/getListByCityID", array($result['info']['city']));
				if(!$area['status']){
					LogRecord($city['info'], __FILE__.__LINE__);
				}
				$this->assign("area",$area['info']);
			}
			$this->assign("address",$result['info']);
			
			$result = apiCall("Tool/Province/queryNoPaging", array());
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->assign("provinces",$result['info']);
			
			$this->display();
		}else{
			$id = I('post.id',0,'intval');
			$province = I('post.province','');
			$city = I('post.city','');
			$area = I('post.area','');
			$detailinfo = I('post.detail','');
			$mobile = I('post.mobile','');
			$postcode = I('post.postcode','');			
			$contactname = I('post.name','');
			
			$entity = array(
				'wxno'=>'',
				'country'=>'中国',
				'province'=>$province,
				'city'=>$city,
				'area'=>$area,
				'detailinfo'=>$detailinfo,
				'mobile'=>$mobile,
				'default'=>1,
				'contactname'=>$contactname,			
				'postcode'=>$postcode,	
			);
			
			
			if(empty($id)){
				//新增
				$entity['wxuserid'] = $this->userinfo['id'];
				$result = apiCall("Shop/Address/add",array($entity));
			}else{
				//保存
				$result = apiCall("Shop/Address/saveByID",array($id,$entity));
			}
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("操作成功!~");
			
		}
	}
	
}
