<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Controller;
use Think\Controller;

class TestCityController extends Controller{
	
	public function index(){
		$result = apiCall("Tool/Province/queryNoPaging",array());
		if($result['status']){
			$this->assign("provinces",$result['info']);		
			$this->display();
		}
		
	}
	
	public function getCitys(){
		
		$provinceID = I('post.provinceid','');
		
		$result = apiCall("Tool/City/getListByProvinceID",array($provinceID));
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
//		if($result['status']){
//			$this->assign("citys",$result['info']);		
//			$this->display();
//		}else{
//			
//		}
	}
	
}
