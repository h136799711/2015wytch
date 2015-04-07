<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Api;
use Common\Api\Api;
use Tool\Model\AreaModel;


class AreaApi extends Api{
	
	protected function _init(){
		$this->model = new AreaModel();
	}
	
	/**
	 * 根据省id获取城市
	 */
	public function getListByCityID($cityid){
		$map['father']= $cityid;
//		$map['city']  = array(array('neq','县'),array('neq','市辖区'),'and'); 
		return $this->queryNoPaging(array($map),"areaID desc","areaID,area");
	}
	
}
