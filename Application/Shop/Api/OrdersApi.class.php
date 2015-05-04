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
	 * 月销量统计
	 * @param $p_ids 商品ID数组
	 */
	public function monthlySales($p_ids){
		
		$currentTime = time();
		$prevTime = $currentTime - 24*3600*30;
		if(count($p_ids) == 0){
			$p_ids = array(-1);
		}
		$map['ord_it.p_id'] = array('in',$p_ids);
		$map['ord.createtime'] = array(array('gt',$prevTime),array('lt',$currentTime));
		$result = $this->model->field("count(ord_it.p_id ) as sales,p_id")->alias(" ord ")->join(" LEFT JOIN __ORDERS_ITEM__ as ord_it on ord.id = ord_it.orders_id and (ord.pay_status = 1 or ord.pay_status = 5) ")->where($map)->group("ord_it.p_id")->fetchSql(false)->select();
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}
		
		return $this->apiReturnSuc($result);
	}
	
	
	/**
	 * 事务增加订单信息
	 */
	public function addOrder($entity){
//		addWeixinLog($entity['items'],'add order 订单 0');
//		addWeixinLog($entity['items'],'add order 订单 0.0');
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
//				'items'=>'',
				'items'=>serialize($entity['items']),
			 );
//		addWeixinLog($order,'add order 订单 1');
		
		$this->model->startTrans();
		$result = $this->add($order);
		
//		addWeixinLog($result,'add order 订单 2');
		
		$orderid = '';
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
			//上面的都没有错误
			//3. 插入到orders_item表中
			$products = $entity['items']['products'];
			$items_arr = array();
			$currentTime = time();
			foreach($products as $vo){
				$tmp = array(
					'orders_id'=>$orderid,
					'has_sku'=>$vo['has_sku'],
					'p_id'=>$vo['p_id'],
					'name'=>$vo['name'],
					'ori_price'=>$vo['ori_price'],
					'price'=>$vo['price'],
					'img'=>$vo['img'],
					'count'=>$vo['count'],
					'post_price'=>$vo['post_price']*100.0,
					'sku_id'=>'',
					'sku_desc'=>'',
					'createtime'=>$currentTime
				);
				
				if(intval($vo['has_sku']) == 1){
					//
					$tmp['sku_id'] = $vo['sku_id'];
					$tmp['sku_desc'] = $vo['sku_desc']['sku_desc'];
					
					$tmp['ori_price']= $vo['sku_desc']['ori_price'];
					$tmp['price']= $vo['sku_desc']['price'];
					
					if(!empty($vo['sku_desc']['icon_url'])){
						$tmp['img'] = $vo['sku_desc']['icon_url'];
					}
					
				}
				array_push($items_arr,$tmp);
			}
			
			 $model = new \Common\Model\OrdersItemModel();
//			 addWeixinLog($items_arr,"add");
			 $result = $model->addAll($items_arr);
			 
			 if($result === false){
			 	//新增失败
			 	$flag = false;
				$error = $model->getDbError();
			 }
			 
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


	
	
  }
