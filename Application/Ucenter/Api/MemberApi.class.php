<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Api;

use Ucenter\Model\MemberModel;

class MemberApi extends \Common\Api\Api{
	
	//初始化
	protected function _init(){
		$this->model = new MemberModel();
	}
	
	public function queryByGroup($map,$page){
		$result = $this->model->queryByGroup($map,$page);
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}
}
