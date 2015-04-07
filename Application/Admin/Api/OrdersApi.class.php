<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


  namespace Admin\Api;
  use Common\Api\Api;
  use Common\Model\OrdersModel;
  
  class OrdersApi extends Api{
  	
  	protected function _init(){
  		$this->model = new OrdersModel();
  	}
	
	/**
	 * 设置订单状态
	 * TODO：需要锁定数据行写操作
	 */
	public function saveOrderStatus($orderid,$orderstatus){
		$model = new OrdersModel();
		$result = $model->where(array('orderid'=>$orderid))->save(array('order_status'=>$orderstatus));
		if($result === FALSE){
			$error = $model->getDbError();
			return $this->apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}
	
 }
