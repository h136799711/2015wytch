<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


  namespace Shop\Api;
  use Common\Api\Api;
  use Common\Model\OrdersItemModel;
  
  class OrdersItemApi extends Api{
  	protected function _init(){
  		$this->model = new OrdersItemModel();
  	}
	
		
	/**
	 * 统计单个商品的月销量
	 */
	public function monthlySales($p_id){
		
		//TODO: 当前计算不管是否成交，都计入销量
		$map['p_id'] =$p_id;
		$currentTime = time();
		$map['createtime'] = array(array('gt',$currentTime - 30*3600*24),array('lt',$currentTime));
		
		$result = $this->model->where($map)->select();
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}
		if(is_null($result)){
			//空无任何销量
			return $this->apiReturnSuc(0);
		}
		$orders_id_arr = array();
		
		foreach($result as $vo){
			array_push($orders_id_arr,$vo['orders_id']);
		}
		if(count($orders_id_arr) == 0){	
			return $this->apiReturnSuc(0);
		}
		
		$model = new \Common\Model\OrdersModel();
		$mapOrders = array();
		$mapOrders['id'] = array('in',$orders_id_arr);
		$mapOrders['pay_status'] = \Common\Model\OrdersModel::ORDER_PAID;
		
		$result = $model->where(array($mapOrders))->select();
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}
		
		$orders_ids = array(-1);
		foreach($result as $vo){
			array_push($orders_ids,$vo['id']);
		}
		
		$map['orders_id'] = array('in',$orders_ids);
		
		$result = $this->model->where($map)->count();
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}
		
		return $this->apiReturnSuc($result);
		
	}
	
	
  }
