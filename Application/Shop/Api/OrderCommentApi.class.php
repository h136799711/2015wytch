<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Api;
use Common\Model\OrderCommentModel;
use \Common\Api\Api;
class OrderCommentApi extends Api{
	protected function _init(){
		$this->model = new OrderCommentModel();
	}
	
	/**
	 * 添加多个评分
	 * @param $orders_id 订单ID
	 * @param $uid 用户ID
	 * @param $pid_arr 产品数组－评分
	 * @param $score_arr 评分数组
	 * @param $text_arr 评价内容
	 */
	public function addArray($orders_id,$uid,$pid_arr,$score_arr,$text_arr){
		
		$this->model->startTrans();
		
		$flag = true;
		$error = "";
		$insert_ids = array();
		$nowtime = time();
		foreach($pid_arr as $key=>$id){
			$entity = array(
				'product_id'=>$id,
				'orders_id'=>$orders_id,
				'score'=>$score_arr[$key],
				'comment'=>$text_arr[$key],
				'user_id'=>$uid,
				'createtime'=>$nowtime
			);
			
			if($this->model->create($entity,1)){
				
				$result = $this->model->add($entity);
				
				if($result === false){
					$flag = false;
					$error = $this->model->getDbError();
				}else{
					array_push($insert_ids,$result);
				}
				
			}else{
				$flag = false;
				$error = $this->model->getError();
			}
			
				
		}
		
		
		if($flag){
			$this->model->commit();
			return $this->apiReturnSuc($insert_ids);
		}else{
			$this->model->rollback();
			return $this->apiReturnErr($error);
		}
		
	}
}

