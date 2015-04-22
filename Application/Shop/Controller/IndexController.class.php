<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

class IndexController extends ShopController{
	
	protected function _initialize(){
		parent::_initialize();
	}
	
	/**
	 * 首页
	 */
	public function index(){
		$map= array('uid'=>$this->wxaccount['uid'],'storeid'=>-1,'position'=>C("DATATREE.SHOP_INDEX_BANNERS"));
		
		$page = array('curpage'=>0,'size'=>8);
		$order = "createtime desc";
		$params = false;
		
		$result = apiCall("Shop/Banners/query",array($map,$page,$order,$params));
//		dump($result);
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->assign("banners",$result['info']['list']);
		
		$this->display();
	}
	
}

