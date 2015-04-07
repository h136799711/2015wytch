<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Api;

use Common\Model\WxuserGroupModel;
use Common\Model\GroupAccessModel;

class WxuserGroupApi extends \Common\Api\Api{
		
	protected function _init(){
		$this->model = new WxuserGroupModel();
	}
	
	/**
	 * 添加实体并增加groupaccess
	 */
	public function addWithAccess($entity){
		$this->model->startTrans();
		$result = FALSE;
		$result2 = FALSE;
		$error = "";
		if($this->model->create($entity)){
			$result = $this->model->add();
		}else{
			$error = $this->model->getError();
		}
		
		if($result > 0){
			$groupaccess = D('GroupAccess');
			if($groupaccess->create(array('wxuser_group_id'=>$result))){
				$result2 =  $groupaccess->add();
			}else{
				$error = $groupaccess->getError();
			}
		}
		if($result === FALSE || $result2 === FALSE){
			if(empty($error)){
				if($result === FALSE){
					$error = $this->model->getDbError();
				}elseif($result2 === FALSE){
					$error = $groupaccess->getDbError();
				}
			}
			$this->model->rollback();
			return $this->apiReturnErr($error);
		}else{
			$this->model->commit();
			return $this->apiReturnSuc($result);
		}

	}
	
	
	
	/**
	 * 删除用户组，同时删除其对应权限
	 */
	public function delWithAccess($id){
		$this->model->startTrans();
		$result = FALSE;
		$result2 = FALSE;
		
		$groupaccess = D('GroupAccess');
		$result2 =  $groupaccess->where("wxuser_group_id = $id")->delete();
		
		$result = $this->model->where("id = $id")->delete();
		
		if($result === false || $result2 === false){
			if($result === FALSE){
				$error = $this->model->getDbError();
			}elseif($result2 === FALSE){
				$error = $groupaccess->getDbError();
			}
			$this->model->rollback();
			return $this->apiReturnErr($error);
		}else{
			$this->model->commit();
			return $this->apiReturnSuc($result);
		}
	}
	
}
