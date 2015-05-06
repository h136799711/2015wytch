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
	 * 订单支付－货到付款操作
	 * @param ids 订单id数组
	 */
	public function cashOndelivery($ids,$isauto,$uid){
		$orderStatusHistoryModel = new \Common\Model\OrderStatusHistoryModel();
		//
		foreach($ids as  $id){
		
			$result = $this->model->where(array('id'=>$id))->find();
			
			if($result == false){
				return $this->returnErr($this->model->getDbError());			
			}
			
			if(is_null($result)){
				return $this->returnErr("订单ID错误!");			
			}
			
			if($result['pay_status'] != \Common\Model\OrdersModel::ORDER_TOBE_PAID){
				return $this->returnErr("当前订单状态无法变更！");
			}
			
			$entity = array(
				'reason'=>"用户选择货到付款支付!",
				'orders_id'=>$result['orderid'],
				'operator'=>$uid,
				'status_type'=>'PAY',
				'cur_status'=>$result['pay_status'],
				'next_status'=>\Common\Model\OrdersModel::ORDER_CASH_ON_DELIVERY,
			);
			
			$this->model->startTrans();
			$flag = true;
			$return = "";
			
			$result = $this->model->where(array('id'=>$id))->save(array('pay_status'=>\Common\Model\OrdersModel::ORDER_CASH_ON_DELIVERY));
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
		//单个
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
		
		if($result['order_status'] != \Common\Model\OrdersModel::ORDER_TOBE_SHIPPED){
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
		
		if($result['order_status'] != \Common\Model\OrdersModel::ORDER_TOBE_CONFIRMED){
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
//		dump($result);
		if($result['order_status'] != \Common\Model\OrdersModel::ORDER_TOBE_CONFIRMED){
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
			'reason'=>"确认收货操作!",
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

	
	/**
	 * 退货操作
	 */
	public function returned($id,$isauto,$uid){
		$orderStatusHistoryModel = new \Common\Model\OrderStatusHistoryModel();
		$result = $this->model->where(array('id'=>$id))->find();
		
		if($result == false){
			return $this->returnErr($this->model->getDbError());			
		}
		
		if(is_null($result)){
			return $this->returnErr("订单ID错误!");			
		}
		
		if($result['order_status'] == \Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS ){
			return $this->returnErr("当前订单状态出错!");
		}
		
		$entity = array(
			'reason'=>"订单退货操作!",
			'orders_id'=>$result['orderid'],
			'operator'=>$uid,
			'status_type'=>'ORDER',
			'cur_status'=>$result['order_status'],
			'next_status'=>\Common\Model\OrdersModel::ORDER_RETURNED,
		);
		
		$this->model->startTrans();
		$flag = true;
		$return = "";
		
		$result = $this->model->where(array('id'=>$id))->save(array('order_status'=>\Common\Model\OrdersModel::ORDER_RETURNED));
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
	 * 订单评价操作
	 */
	public function evaluation($id,$isauto,$uid){
		$orderStatusHistoryModel = new \Common\Model\OrderStatusHistoryModel();
		$result = $this->model->where(array('id'=>$id))->find();
		
		if($result == false){
			return $this->returnErr($this->model->getDbError());			
		}
		
		if(is_null($result)){
			return $this->returnErr("订单ID错误!");			
		}
		
		if($result['order_status'] != \Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS ){
			return $this->returnErr("当前订单状态出错!");
		}
		
		$entity = array(
			'reason'=>"订单评价操作!",
			'orders_id'=>$result['orderid'],
			'operator'=>$uid,
			'status_type'=>'COMMENT',
			'cur_status'=>$result['order_status'],
			'next_status'=>\Common\Model\OrdersModel::ORDER_COMPLETED,
		);
		
		$this->model->startTrans();
		$flag = true;
		$return = "";
		
		$result = $this->model->where(array('id'=>$id))->save(array('comment_status'=>\Common\Model\OrdersModel::ORDER_HUMAN_EVALUATED,'order_status'=>\Common\Model\OrdersModel::ORDER_COMPLETED));
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

