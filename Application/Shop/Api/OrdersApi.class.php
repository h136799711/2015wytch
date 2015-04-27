<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


  namespace Shop\Api;
  use Common\Api\Api;
  use Common\Model\OrdersModel;
  
  class OrdersApi extends Api{
  	protected function _init(){
  		$this->model = new OrdersModel();
  	}
	
	/**
	 * 事务增加订单信息
	 */
	public function addOrder($entity){
		$this->model->startTrans();
		$flag = true;
		$error = "";
		//1. 增加order表记录
		$order = array(
				'storeid'=>$entity['storeid'],
				'wxaccountid'=>$entity['wxaccountid'],
				'wxuser_id' => $entity['wxuser_id'], 
				'price' => $entity['price'], 
				'note' => $entity['note'], 
				'orderid' => $entity['orderid'], 
				'items' => $entity['items']
			 );
		$result = $this->add($order);
		if($result['status']){
		$orderid = $result['info'];
		//2. 增加order_contactinfo记录
		$orderContactInfo = array(
				'wxuser_id' => $entity['wxuser_id'], 
				'orderid' => $entity['orderid'], 
				'mobile' => $entity['mobile'], 
				'wxno' => $entity['wxno'], 
				'contactname' => $entity['contactname'], 
				'country' => $entity['country'], 
				'province' => $entity['province'], 
				'city' => $entity['city'], 
				'area' => $entity['area'], 
				'wxno' => $entity['wxno'], 
				'detailinfo' => $entity['detailinfo'], 
			);
			 $model = new \Common\Model\OrdersContactinfoModel();
			 $result = $model->create($orderContactInfo,1);
			 
			 if($result){
			 	$result = $model->add();
			 	if($result === FALSE){
			 		//新增失败
			 		$flag = false;
					$error = $model->getDbError();
			 	}
				
			 }else{//自动验证失败
			 	$flag = false;
				$error = $model->getError();
			 }
			 
		}else{
			$flag = false;
			$error = $result['info'];
		}
			 
			 
		if($flag){
			$this->model->commit();
			return $this->apiReturnSuc($orderid);
		}else{
			$this->model->rollback();
			return $this->apiReturnErr($error);
		}
		
	}
	
	
	/**
	 * 设置支付状态
	 * TODO：需要锁定数据行写操作
	 * @param $orderid  数组
	 */
	public function savePayStatus($map,$paystatus){
		
		
		$result = $this->model->where($map)->lock(true)->save(array('pay_status'=>$paystatus));
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}

	/**
	 * 设置订单状态
	 */
	public function saveOrderStatus($orderid,$orderstatus){
		$result = $this->model->where(array('orderid'=>$orderid))->save(array('order_status'=>$orderstatus));
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}
	
	
	
  }
