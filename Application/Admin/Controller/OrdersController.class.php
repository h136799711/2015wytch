<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class OrdersController extends AdminController {
	/**
	 * 初始化
	 */
	protected function _initialize() {
		parent::_initialize();
	}
	
	/**
	 * 统计
	 */
	public function statics(){
		
		$this->display();
	}

	/**
	 * 订单管理
	 */
	public function index() {
		$arr = getDataRange(3);
		$payStatus = I('paystatus', '');
		$orderStatus = I('orderstatus', '');
		$orderid = I('post.orderid', '');
		$userid = I('uid', 0);
		$startdatetime = urldecode($arr[0]);
		//I('startdatetime', , 'urldecode');
		$enddatetime = urldecode($arr[1]);
		//I('enddatetime',   , 'urldecode');

		//分页时带参数get参数
		$params = array('startdatetime' => $startdatetime, 'enddatetime' => ($enddatetime),'wxaccountid'=>getWxAccountID());

		$startdatetime = strtotime($startdatetime);
		$enddatetime = strtotime($enddatetime);

		if ($startdatetime === FALSE || $enddatetime === FALSE) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error(L('ERR_DATE_INVALID'));
		}

		$map = array();
		$map['wxaccountid'] = getWxAccountID();
		if (!empty($orderid)) {
			$map['orderid'] = array('like', $orderid . '%');

		}
		if ($payStatus != '') {
			$map['pay_status'] = $payStatus;
			$params['paystatus'] = $payStatus;
		}
		if ($orderStatus != '') {
			$map['order_status'] = $orderStatus;
			$params['orderstatus'] = $orderStatus;
		}
		$map['createtime'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');

		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";

		if ($userid > 0) {
			$map['wxuser_id'] = $userid;
		}
		//		$result = apiCall("Admin/Wxuser/queryNoPaging", array(array(),false,"id,nickname,avatar") );
		//		if($result['status']){
		//			$this->assign("users",$result['info']);
		//		}

		//
		$result = apiCall('Admin/OrdersInfoView/query', array($map, $page, $order, $params));

		//
		if ($result['status']) {
			$this -> assign('orderid', $orderid);
			$this -> assign('orderStatus', $orderStatus);
			$this -> assign('payStatus', $payStatus);
			$this -> assign('startdatetime', $startdatetime);
			$this -> assign('enddatetime', $enddatetime);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	/**
	 * 订单确认
	 */
	public function sure() {
		$orderid = I('orderid', '');
		$userid = I('uid', 0);
		$params = array();
		$map = array();
		$map['order_status'] = \Common\Model\OrdersModel::ORDER_TOBE_CONFIRMED;
		$map['pay_status'] = \Common\Model\OrdersModel::ORDER_PAID;
		$map['wxaccountid']=getWxAccountID();
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";

		if (!empty($orderid)) {
			$map['orderid'] = array('like', $orderid . '%');
			$params['orderid'] = $orderid;
		}
		if ($userid > 0) {
			$map['wxuser_id'] = $userid;
			$params['uid'] = $userid;
		}

		//
		$result = apiCall('Admin/OrdersInfoView/query', array($map, $page, $order, $params));

		//
		if ($result['status']) {
			$this -> assign('orderid', $orderid);
			$this -> assign('orderStatus', $orderStatus);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	/**
	 * 发货
	 */
	public function deliverGoods() {
		$orderStatus = I('orderstatus','3');
		$orderid = I('orderid', '');
		$userid = I('uid', 0);
		$params = array();

		$map = array();
		$map['wxaccountid']=getWxAccountID();
		$params['wxaccountid'] = $map['wxaccountid'];
		if (!empty($orderid)) {
			$map['orderid'] = array('like', $orderid . '%');
			$params['orderid'] = $orderid;
		}
		//		if($payStatus != ''){
		//			$map['pay_status'] = $payStatus;
		//		}
		if($orderStatus != ''){
			$map['order_status'] = $orderStatus;
			$params['order_status'] = $orderStatus;
		}
//		$map['order_status'] = \Common\Model\OrdersModel::ORDER_TOBE_SHIPPED;
		//		$map['createtime'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');
		$map['pay_status'] = \Common\Model\OrdersModel::ORDER_PAID;

		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";

		if ($userid > 0) {
			$map['wxuser_id'] = $userid;
		}

		//
		$result = apiCall('Admin/OrdersInfoView/query', array($map, $page, $order, $params));

		//
		if ($result['status']) {
			$this -> assign('orderid', $orderid);
			$this -> assign('orderStatus', $orderStatus);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	/**
	 * 查看
	 */
	public function view() {
		if (IS_GET) {
			$id = I('get.id', 0);
			$map = array('id' => $id);
			$result = apiCall("Admin/OrdersInfoView/getInfo", array($map));
			if ($result['status']) {
				$this -> assign("items", unserialize($result['info']['items']));
				$this -> assign("order", $result['info']);
				$this -> display();
			} else {
				$this -> error($result['info']);
			}
		}
	}

	/**
	 * 单个发货操作
	 */
	public function deliver() {
		$expresslist = C("CFG_EXPRESS");
		if (IS_GET) {
			$id = I('get.id',0);
			$map = array('id'=>$id);
			$result = apiCall("Admin/OrdersInfoView/getInfo", array($map));
			if($result['status']){
				$this->assign("order",$result['info']);
			}else{
				$this->error("订单信息获取失败！");
			}
			
			$map = array('orderid'=>$result['info']['orderid']);
			$result = apiCall("Admin/OrdersExpress/getInfo", array($map));
			if($result['status'] && is_array($result['info'])){
				$this->assign("express",$result['info']);
			}
			$this->assign("expresslist",$expresslist);
			$this->display();
		} elseif (IS_POST) {
			$expresscode = I('post.expresscode','');
			$expressno = I('post.expressno','');
			$wxuserid = I('post.wxuserid',0);
			$orderid = I('post.orderid','');
			if(empty($expresscode) || !isset($expresslist[$expresscode])){
				$this->error("快递信息错误！");
			}
			if(empty($expressno)){
				$this->error("快递单号不能为空");
			}
			$id = I('post.id',0);
			$entity = array(
				  'expresscode'=>$expresscode,
				  'expressname'=>$expresslist[$expresscode],
				  'expressno'=>$expressno,
				  'note'=>I('post.note',''),
				  'orderid'=>$orderid,
				  'wxuserid'=>$wxuserid,
			);
			
			if(empty($entity['orderid'])){
				$this->error("订单编号不能为空");
			}
			if(empty($id) || $id <= 0){
				$result = apiCall("Admin/OrdersExpress/add", array($entity));
			}else{
				$result = apiCall("Admin/OrdersExpress/saveByID", array($id,$entity));
			}
			
			
			if($result['status']){
				
				$text = "亲，您订单($orderid)已经发货，快递单号：$expressno,快递公司：".$expresslist[$expresscode].",请注意查收";
				//DONE:
				// 1.发送提醒信息给指定用户
				$this->sendTextTo($wxuserid,$text);
				// 2. 修改订单状态为已发货
				$orderstatus = \Common\Model\OrdersModel::ORDER_SHIPPED;
				$result = apiCall("Admin/Orders/saveOrderStatus", array($entity['orderid'],$orderstatus));
				
				$this->success(L('RESULT_SUCCESS'));
			}else{
				$this->error($result['info']);
			}
		}
	}
	

	/**
	 * 退货管理
	 */
	public function returned() {
//		if (IS_GET) {
			$orderid = I('orderid', '');
			$userid = I('uid', 0);
			$orderStatus = I('orderstatus',\Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS);
			$params = array();
			$map = array();
			if (!empty($orderid)) {
				$map['orderid'] = array('like', $orderid . '%');
				$params['orderid'] = $orderid;
			}
			$map['wxaccountid'] = getWxAccountID();
			$map['order_status'] = $orderStatus;
			$map['pay_status'] = \Common\Model\OrdersModel::ORDER_PAID;
			
			$params['order_status'] = $orderStatus;
			$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
			$order = " createtime desc ";

			if ($userid > 0) {
				$map['wxuser_id'] = $userid;
			}

			//
			$result = apiCall('Admin/OrdersInfoView/query', array($map, $page, $order, $params));

			//
			if ($result['status']) {
				$this -> assign('orderid', $orderid);
				$this -> assign('orderStatus', $orderStatus);
				$this -> assign('show', $result['info']['show']);
				$this -> assign('list', $result['info']['list']);
				$this -> display();
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
//		} elseif (IS_POST) {
//			
//		}
	}
	
	/**
	 * 单个退货操作
	 */
	public function returnGoods(){
		if(IS_GET){
			$id = I('get.id',0);
			$this->assign("id",$id);
			$this->display();
		}elseif(IS_POST){
			$id = I('post.id',0);
			$entity = array('order_status'=>\Common\Model\OrdersModel::ORDER_RETURNED,'status_note'=>'|[退货]'.I('post.note',''));
			$result = apiCall("Admin/Orders/saveByID",array($id,$entity) );
			if($result['status']){
				$this->success("操作成功！",U('Admin/Orders/returned'));
			}else{
				$this->error($result['info']);
			}
		}
	}

	
	/**
	 * 批量发货
	 * TODO:批量发货
	 */
	public function bulkDeliver(){
		$this->error("功能开发中...");
	}

	/**
	 * 批量确认订单
	 */
	public function bulkSure() {
		if (IS_POST) {

			$ids = I('post.ids', -1);
			if ($ids === -1) {
				$this -> error(L('ERR_PARAMETERS'));
			}
			$ids = implode(',', $ids);
			$map = array('id' => array('in', $ids));
			$entity = array('order_status' => \Common\Model\OrdersModel::ORDER_TOBE_SHIPPED);
			$result = apiCall("Admin/Orders/save", array($map, $entity));
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/Orders/sure'));
			} else {
				$this -> error($result['info']);
			}
		}
	}


	private function sendTextTo($wxuserid,$text){
		//
		$wxaccountid = getWxAccountID();
		$result = apiCall("Admin/Wxuser/getInfo",array(array("id"=>$wxuserid)));
		$wxaccount = apiCall("Admin/Wxaccount/getInfo", array(array("id"=>$wxaccountid)));
		$openid = "";
		if($result['status'] && is_array($result['info'])){
			$openid = $result['info']['openid'];
		}
		if($wxaccount['status'] && is_array($wxaccount['info'])){
			$appid =  $wxaccount['info']['appid'];
			$appsecret =  $wxaccount['info']['appsecret'];				
			$wxapi = new \Common\Api\WeixinApi($appid,$appsecret);
			$wxapi->sendTextToFans($openid, $text);
			$wxapi->sendTextToFans($openid, $text);//发2次
		}
	}

}
