<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Api;
use Common\Api\Api;
use Tool\Model\CityModel;


class CityApi extends Api{
	
	protected function _init(){
		$this->model = new CityModel();
	}
	
	/**
	 * 根据省id获取城市
	 */
	public function getListByProvinceID($provinceId){
		$map['father']= $provinceId;
		$map['city']  = array(array('neq','县'),array('neq','市辖区'),'and'); 
		return $this->queryNoPaging(array($map),"cityID desc","cityID,city");
	}
	
}
