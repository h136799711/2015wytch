<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Api;
use Common\Api\Api;
use Ucenter\Model\ConfigModel;

class ConfigApi extends Api{
	
	protected function _init(){
		$this->model = new ConfigModel();
	}
	
	/**
	 * 设置
	 */
	public function set($config){
		$result = $this->model->set($config);
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		} 
		else{
			return $this->apiReturnSuc($result);
		}
	}
	
}
