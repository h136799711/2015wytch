<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Ucenter\Controller;

class UcenterAppController extends UcenterController{
	
	public function index(){
		$map = null;
		
		$page = array('curpage'=>I('get.p'),C('LIST_ROWS'));
		
		$result = apiCall("UcenterApp/query", array($map,$page));
		if($result['status']){
			$this->assign("list",$result['info']['list']);
			$this->assign("show",$result['info']['show']);
			$this->display();
		}
	}
	
	
	
}
