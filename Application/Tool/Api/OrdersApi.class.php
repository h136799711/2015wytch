<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


  namespace Tool\Api;
  
  use Common\Api\Api;
  use Common\Model\OrdersModel;
  
  class OrdersApi extends Api{
  	protected function _init(){
  		$this->model = new OrdersModel();
  	}
	


	/**
	 * 设置订单状态
	 * TODO：需要锁定数据行写操作
	 * @param $interval 判断的间隔时间 秒 为单位
	 */
	public function orderStatusToCancel($interval){
		$map['updatetime'] = array('lt',time()-$interval);
		$map['order_status'] = OrdersModel::ORDER_TOBE_CONFIRMED;
		$map['pay_status'] = OrdersModel::ORDER_TOBE_PAID;
		$saveEntity = array('order_status'=>OrdersModel::ORDER_CANCEL);
		$result = $this->model->create($saveEntity,2);
		if($result === false){
			return $this->apiReturnErr($this->model->getError());
		}
		$result = $this->model->where($map)->lock(true)->save();
		addWeixinLog($this->model->getLastSql(),"[自动变更订单待确认、待支付为已取消SQL]");
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}

	/**
	 * 设置订单状态
	 * TODO：需要锁定数据行写操作
	 * @param $interval 判断的间隔时间 秒 为单位
	 */
	public function orderStatusToRecieved($interval){
		$map['updatetime'] = array('lt',time()-$interval);
		$map['order_status'] = OrdersModel::ORDER_SHIPPED;
		$saveEntity = array('order_status'=>OrdersModel::ORDER_RECEIPT_OF_GOODS);
		$result = $this->model->create($saveEntity,2);
		if($result === false){
			return $this->apiReturnErr($this->model->getError());
		}
		$result = $this->model->where($map)->lock(true)->save();
		addWeixinLog($this->model->getLastSql(),"[自动变更订单已发货为已收货SQL]");
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}
	
	/**
	 *
	 * 设置订单状态
	 * TODO：需要锁定数据行写操作
	 * @param $interval 判断的间隔时间 秒 为单位
	 * 
	 */
	public function orderStatusToCompleted($interval){
		$map['updatetime'] = array('lt',time()-$interval);
		$map['order_status'] = OrdersModel::ORDER_RECEIPT_OF_GOODS;
		$saveEntity = array('order_status'=>OrdersModel::ORDER_COMPLETED);
		$result = $this->model->create($saveEntity,2);
		if($result === false){
			return $this->apiReturnErr($this->model->getError());
		}
		$result = $this->model->where($map)->lock(true)->save();
		addWeixinLog($this->model->getLastSql(),"[自动变更订单已收货为已完成SQL]");
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}
	
	
	
  }
