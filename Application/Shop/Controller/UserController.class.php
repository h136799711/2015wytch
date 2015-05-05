<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

class UserController extends ShopController{
	
	
	
	/**
	 * 用户个人中心
	 */	
	public function info(){
		if(IS_GET){
			
			$tobePaid = $this->orderCount(1);
			$tobeShipped = $this->orderCount(2);
			$tobeReceipt = $this->orderCount(3);
			$tobeEval = $this->orderCount(4);
			
			$this->assign("tobepaid",$tobePaid);
			$this->assign("tobeshipped",$tobeShipped);
			$this->assign("tobereceipt",$tobeReceipt);
			$this->assign("tobeeval",$tobeEval);
			
			$rank = convert2LevelImg($this->userinfo['exp']);
			$this->assign("rank",$rank);
			$this->display();
		}
	}
	
	/**
	 * 个人订单
	 */
	public function order(){
		
		if(IS_GET){
			$datatype = I('get.datatype',0);
			$this->assign("datatype",$datatype);
			$this->display();
		}
		
	}
	
	
	private function orderCount($type){
		
		$map = array();
		if ($type == 1) {
			//待付款
			$map['pay_status'] = \Common\Model\OrdersModel::ORDER_TOBE_PAID;
		} elseif($type != 0) {
			//货到付款，在线已支付
			$map['pay_status'] = array('in', array(\Common\Model\OrdersModel::ORDER_PAID, \Common\Model\OrdersModel::ORDER_CASH_ON_DELIVERY));

		}

		if ($type == 2) {
			//1. 已支付、货到付款
			//2. 待发货
			//
			$map['order_status'] = \Common\Model\OrdersModel::ORDER_TOBE_SHIPPED;

		} elseif ($type == 3) {
			//1. 已支付、货到付款
			//2. 已发货
			$map['order_status'] = \Common\Model\OrdersModel::ORDER_SHIPPED;
			$shouldGetExpressInfo = true;
		} elseif ($type == 4) {
			//1. 已支付、货到付款
			//2. 已收货
			//3. 待评论
			$map['order_status'] = \Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS;
			$map['comment_status'] = \Common\Model\OrdersModel::ORDER_TOBE_EVALUATE;
			$shouldGetExpressInfo = true;

		}

		$map['wxuser_id'] = $this -> userinfo['id'];
		//TODO: 订单假删除时不查询
		$map['status'] = 1;
		$orders = " createtime desc ";

		$result = apiCall("Shop/Orders/count", array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		return $result['info'];
	}
	
	
	
}

