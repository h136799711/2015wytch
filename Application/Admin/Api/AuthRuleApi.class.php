<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use Common\Api\Api;
use Admin\Model\AuthRuleModel;
class AuthRuleApi extends Api{
	
	protected function _init(){
		$this->model = new AuthRuleModel();
	}
	
	/*
	 * 获取不重复module字段数据
	 *  
	 */
	public function allModules(){
		$result = $this->model->distinct(true)->field('module')->select();
		if($result === false){			
			return $this->apiReturnErr($this->model->getDbError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}
		
}
