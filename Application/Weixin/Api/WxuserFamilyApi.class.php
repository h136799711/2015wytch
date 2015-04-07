<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Api;
use Common\Model\WxuserFamilyModel;

class WxuserFamilyApi extends  \Common\Api\Api{
	
	protected function _init(){
		$this->model = new WxuserFamilyModel();
	}
	
	/**
	 * 根据参数创建一个wxuserfamily记录
	 * 
	 * @return 
	 */
	public function createOneIfNone($wxaccount_id,$openid){
		
		$wxuserfamily = $this->model->where(array('wxaccount_id'=>$wxaccount_id,'openid'=>$openid))->find();
		if($wxuserfamily === false ){
			$error = $this->model->getDbError();
			return $this -> apiReturnErr($error);
		}elseif(is_array($wxuserfamily)){
			//已存在
			return $this -> apiReturnSuc($wxuserfamily['id']);
		}
		
		$entity = array(
			'wxaccount_id'=>$wxaccount_id,
			'openid'=>$openid,
		);
		
		
		return  $this->add($entity);
	}
	
}
