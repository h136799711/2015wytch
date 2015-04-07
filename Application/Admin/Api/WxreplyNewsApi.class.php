<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;

use Common\Model\WxreplyNewsModel;

class WxreplyNewsApi extends \Common\Api\Api{
	
	protected function _init(){
		$this->model = new WxreplyNewsModel();	
	}
	
	/**
	 * 获取所有keyword，不重复
	 */
	public function getKeywords(){
		$result = $this->model->distinct(true)->field('keyword')->select();
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}
}
