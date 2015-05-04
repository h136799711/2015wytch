<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Service;

class OrderService extends Service{
	
	/**
	 * 
	 */
	protected function _initialize(){
		$this->model = new \Common\Model\OrdersModel();
	}
	
	/**
	 * 订单发货操作
	 */
	public function shipped($id,$isauto,$uid){
		$orderStatusHistoryModel = new \Common\Model\OrderStatusHistoryModel();
		$result = $this->model->where(array('id'=>$id))->find();
		
		if($result == false){
			return $this->returnErr($this->model->getDbError());			
		}
		
		if(is_null($result)){
			return $this->returnErr("订单ID错误!");			
		}
		
		if($result['order_status'] != 3){
			return $this->returnErr("当前订单状态无法变更！");
		}
		
		$entity = array(
			'reason'=>"订单发货操作!",
			'orders_id'=>$result['orderid'],
			'operator'=>$uid,
			'status_type'=>'ORDER',
			'cur_status'=>$result['order_status'],
			'next_status'=>\Common\Model\OrdersModel::ORDER_SHIPPED,
		);
		
		$this->model->startTrans();
		$flag = true;
		$return = "";
		
		$result = $this->model->where(array('id'=>$id))->save(array('order_status'=>\Common\Model\OrdersModel::ORDER_SHIPPED));
		if($result === false){
			$flag = false;
			$return = $this->model->getDbError();
		}
		if($result == 0){
			$flag = false;
			$return = "订单ID有问题!";
		}
		
		if($orderStatusHistoryModel->create($entity,1)){
			$result = $orderStatusHistoryModel->add();
			if($result === false){
				$flag = false;
				$return = $orderStatusHistoryModel->getDbError();
			}
		}else{
			$flag = false;
			$return = $orderStatusHistoryModel->getError();
		}
		
		
		
		if($flag){
			$this->model->commit();
			return $this->returnSuc($return);
		}else{
			$this->model->rollback();
			return $this->returnErr($return);
		}
		
	}
	
	
	/**
	 * 订单确认
	 */
	public function sureOrder($id,$isauto,$uid){
		$orderStatusHistoryModel = new \Common\Model\OrderStatusHistoryModel();
		$result = $this->model->where(array('id'=>$id))->find();
		
		if($result == false){			
			return $this->returnErr($this->model->getDbError());			
		}
		
		if(is_null($result)){
			return $this->returnErr("订单ID错误!");			
		}
		
		if($result['order_status'] != 2){
			return $this->returnErr("当前订单状态无法变更！");
		}
		
		$entity = array(
			'reason'=>"订单确认操作!",
			'orders_id'=>$result['orderid'],
			'operator'=>$uid,
			'status_type'=>'ORDER',
			'cur_status'=>$result['order_status'],
			'next_status'=>\Common\Model\OrdersModel::ORDER_TOBE_SHIPPED,
		);
		
		$this->model->startTrans();
		$flag = true;
		$return = "";
		
		$result = $this->model->where(array('id'=>$id))->save(array('order_status'=>\Common\Model\OrdersModel::ORDER_TOBE_SHIPPED));
		if($result === false){
			$flag = false;
			$return = $this->model->getDbError();
		}
		if($result == 0){
			$flag = false;
			$return = "订单ID有问题!";
		}
		
		if($orderStatusHistoryModel->create($entity,1)){
			$result = $orderStatusHistoryModel->add();
			if($result === false){
				$flag = false;
				$return = $orderStatusHistoryModel->getDbError();
			}
		}else{
			$flag = false;
			$return = $orderStatusHistoryModel->getError();
		}
		
		
		
		if($flag){
			$this->model->commit();
			return $this->returnSuc($return);
		}else{
			$this->model->rollback();
			return $this->returnErr($return);
		}
		
	}
	
	/**
	 * 退回订单
	 */
	public function backOrder($id,$reason,$isauto,$uid){
		
		$orderStatusHistoryModel = new \Common\Model\OrderStatusHistoryModel();
		$result = $this->model->where(array('id'=>$id))->find();
		
		if($result == false){			
			return $this->returnErr($this->model->getDbError());			
		}
		
		if(is_null($result)){
			return $this->returnErr("订单ID错误!");			
		}
		
		if($result['order_status'] != 2){
			return $this->returnErr("当前订单状态无法变更！");
		}
		
		$entity = array(
			'reason'=>$reason,
			'orders_id'=>$result['orderid'],
			'operator'=>$uid,
			'status_type'=>'ORDER',
			'cur_status'=>$result['order_status'],
			'next_status'=>\Common\Model\OrdersModel::ORDER_BACK,
		);
		
		$this->model->startTrans();
		$flag = true;
		$return = "";
		
		$result = $this->model->where(array('id'=>$id))->save(array('order_status'=>\Common\Model\OrdersModel::ORDER_BACK));
		if($result === false){
			$flag = false;
			$return = $this->model->getDbError();
		}
		if($result == 0){
			$flag = false;
			$return = "订单ID有问题!";
		}
		
		if($orderStatusHistoryModel->create($entity,1)){
			$result = $orderStatusHistoryModel->add();
			if($result === false){
				$flag = false;
				$return = $orderStatusHistoryModel->getDbError();
			}
		}else{
			$flag = false;
			$return = $orderStatusHistoryModel->getError();
		}
		
		
		
		if($flag){
			$this->model->commit();
			return $this->returnSuc($return);
		}else{
			$this->model->rollback();
			return $this->returnErr($return);
		}
		
		
	}
	
	/**
	 * 确认收货操作
	 */
	public function confirmReceive($id,$isauto,$uid){
		$orderStatusHistoryModel = new \Common\Model\OrderStatusHistoryModel();
		$result = $this->model->where(array('id'=>$id))->find();
		
		if($result == false){
			return $this->returnErr($this->model->getDbError());			
		}
		
		if(is_null($result)){
			return $this->returnErr("订单ID错误!");			
		}
		
		if($result['order_status'] != \Common\Model\OrdersModel::ORDER_SHIPPED){
			return $this->returnErr("当前订单状态出错!");
		}
		
		$entity = array(
			'reason'=>"订单发货操作!",
			'orders_id'=>$result['orderid'],
			'operator'=>$uid,
			'status_type'=>'ORDER',
			'cur_status'=>$result['order_status'],
			'next_status'=>\Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS,
		);
		
		$this->model->startTrans();
		$flag = true;
		$return = "";
		
		$result = $this->model->where(array('id'=>$id))->save(array('order_status'=>\Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS));
		if($result === false){
			$flag = false;
			$return = $this->model->getDbError();
		}
		if($result == 0){
			$flag = false;
			$return = "订单ID有问题!";
		}
		
		if($orderStatusHistoryModel->create($entity,1)){
			$result = $orderStatusHistoryModel->add();
			if($result === false){
				$flag = false;
				$return = $orderStatusHistoryModel->getDbError();
			}
		}else{
			$flag = false;
			$return = $orderStatusHistoryModel->getError();
		}
		
		
		
		if($flag){
			$this->model->commit();
			return $this->returnSuc($return);
		}else{
			$this->model->rollback();
			return $this->returnErr($return);
		}
	}
	
}

