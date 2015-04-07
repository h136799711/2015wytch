<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Api;
use Common\Api\Api;
use Ucenter\Model\MenuModel;

class MenuApi extends Api{
	
	protected function _init(){
		$this->model = new MenuModel();
	}
	/**
	 * query 不分页
	 * 查询显示状态下的菜单
	 */
	public function queryShowingMenu($map,$order = false){
		return $this->queryNoPaging(array_merge($map,array('hide'=>0)),$order);		
	}
	
	
	
}
