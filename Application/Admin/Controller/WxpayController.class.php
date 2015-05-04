<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;
use \Common\Api\Wxpay;
use \Common\Api\Wxpay\WxPayApi;

class WxpayController extends AdminController {
	
	public function orderQuery(){
		$out_trade_no = I('get.orderid', '');
		if (empty($out_trade_no)) {
			$out_trade_no = I('post.orderid', '');
		}
		$config = C('WXPAY_CONFIG');
		
		$query = new \Common\Api\Wxpay\OrderQuery($config);
		
		$orderQueryResult = ($query->queryByOutTradeNo($out_trade_no));
		
		//商户根据实际情况设置相应的处理流程,此处仅作举例
		if ($orderQueryResult["return_code"] == "FAIL") {
			$this -> assign("error", "通信出错：" . $orderQueryResult['return_msg']);
		} elseif ($orderQueryResult["result_code"] == "FAIL") {
			$this -> assign("error", $orderQueryResult['err_code_des']);
		} else {
			$orderQueryResult['trade_state'] = self::$WXPAY_TRADE_STATE[$orderQueryResult['trade_state']];
			$orderQueryResult['time_end'] = date('Y-m-d H:i:s', strtotime($orderQueryResult['time_end']));
			$this -> assign("orderQueryResult", $orderQueryResult);
		}
		$this -> assign("out_trade_no", $out_trade_no);
		
		$this->display();
		
	}
	

	
	
	public function orderQuery_old() {
		$out_trade_no = I('get.orderid', '');
		if (empty($out_trade_no)) {
			$out_trade_no = I('post.orderid', '');
		}
		
		//退款的订单号
		if (empty($out_trade_no)) {
			$out_trade_no = " ";
			$this -> error("订单号无效！");
		} else {
			if(!$this->check($out_trade_no)){
				$this -> error("订单号无效！");
			}
			$appid = "";
			$appsecrect = "";
			$config = C("WXPAY_CONFIG");
			//使用订单查询接口
			$orderQuery = new \Common\Api\OrderQueryApi($config);
			//设置必填参数
			//appid已填,商户无需重复填写
			//mch_id已填,商户无需重复填写
			//noncestr已填,商户无需重复填写
			//sign已填,商户无需重复填写
			$orderQuery -> setParameter("out_trade_no", "$out_trade_no");
			//商户订单号
			//非必填参数，商户可根据实际情况选填
			//$orderQuery->setParameter("sub_mch_id","XXXX");//子商户号
			//$orderQuery->setParameter("transaction_id","XXXX");//微信订单号

			//获取订单查询结果
			$orderQueryResult = $orderQuery -> getResult();

			//商户根据实际情况设置相应的处理流程,此处仅作举例
			if ($orderQueryResult["return_code"] == "FAIL") {
				LogRecord("[通信出错]", $orderQueryResult['return_msg']);
				//				$this->error("通信出错：" . $orderQueryResult['return_msg'] );
				$this -> assign("error", "通信出错：" . $orderQueryResult['return_msg']);
			} elseif ($orderQueryResult["result_code"] == "FAIL") {
				$this -> assign("error", $orderQueryResult['err_code_des']);
				//				echo "错误代码描述：" . $orderQueryResult['err_code_des'] . "<br>";
			} else {
				//				dump(self::$WXPAY_TRADE_STATE);
				$orderQueryResult['trade_state'] = self::$WXPAY_TRADE_STATE[$orderQueryResult['trade_state']];
				$orderQueryResult['time_end'] = date('Y-m-d H:i:s', strtotime($orderQueryResult['time_end']));
				$this -> assign("orderQueryResult", $orderQueryResult);
			}
			$this -> assign("out_trade_no", $out_trade_no);
			$this -> display();
		}
	}

	static $WXPAY_TRADE_STATE = array('SUCCESS' => '支付成功', 'REFUND' => '转入退款', 'NOTPAY' => '未支付', 'CLOSED' => '已关闭', 'REVOKED' => '已撤销', 'USERPAYING' => '用户支付中', 'NOPAY' => '未支付(输入密码或 确认支付超时)', 'PAYERROR' => '支付失败(其他 原因,如银行返回失败)', );
	
	/**
	 * 下载对账单某天
	 */
	public function downloadBill() {
		$bill_date = I('post.billdate', date('Ymd', time() - 24 * 3600));
		$d = I('get.d', 0);

		if (IS_POST || $d == 1) {
			//对账单日期
			if (empty($bill_date)) {
				$bill_date = date('Ymd', time() - 24 * 3600);
			} else {
//				if(!$this->check($out_trade_no)){
//					$this -> error("订单号无效！");
//				}
				//使用对账单接口
				$config = C("WXPAY_CONFIG");
				//使用订单查询接口
				$downloadBill = new \Common\Api\DownloadBillApi($config);
				//设置对账单接口参数
				//设置必填参数
				//appid已填,商户无需重复填写
				//mch_id已填,商户无需重复填写
				//noncestr已填,商户无需重复填写
				//sign已填,商户无需重复填写
				$downloadBill -> setParameter("bill_date", "$bill_date");
				//对账单日期
				$downloadBill -> setParameter("bill_type", "ALL");
				//账单类型
				//非必填参数，商户可根据实际情况选填
				//$downloadBill->setParameter("device_info","XXXX");//设备号

				//对账单接口结果
				$downloadBillResult = $downloadBill -> getResult();
				//			echo $downloadBillResult['return_code'];
				//			dump($downloadBillResult);
				if ($downloadBillResult['return_code'] == "FAIL") {
					$this -> assign("error", $downloadBillResult['return_msg']);
				} else {
					$table = $this -> bill2Table($downloadBill -> response);
					if ($d == 1) {
						$filename = $bill_date . "-" . time();
						header("Content-type:application/vnd.ms-excel");
						header("Content-Disposition:attachment;filename=$filename.xls");
						//					echo $downloadBill->response;

						$this -> echobill2Table($table);
						exit();
					}
					$this -> assign("header", $table['header']);
					$this -> assign("rows", $table['rows']);
					$this -> assign("footertitle", $table['footer']['title']);
					$this -> assign("footercont", $table['footer']['cont']);
					//				$this->assign("result", $downloadBill -> response);
				}
			}

		}
		$this -> assign("billdate", $bill_date);
		$this -> display();
	}

	private function echobill2Table($table) {

		for ($i = 0; $i < count($table['header']); $i++) {
			echo $table['header'][$i] . chr(9);
		}
		echo chr(13);
		$rows = $table['rows'];
		for ($i = 0; $i < count($rows); $i++) {
			for ($j = 0; $j < count($rows[$i]); $j++) {
				//				if($j == count($rows[$i])-1){
				//					echo $rows[$i][$j];
				//				}else{
				echo $rows[$i][$j] . chr(9);
				//				}
			}
			echo chr(13);
		}
		$footertittle = $table['footer']['title'];
		for ($i = 0; $i < count($footertittle); $i++) {
			echo $footertittle[$i] . chr(9);
		}
		echo chr(13);
		$footercont = $table['footer']['cont'];
		for ($i = 0; $i < count($footercont); $i++) {
			echo $footercont[$i] . chr(9);
		}
		echo chr(13);
	}

	private function bill2Table($bill) {
		$table = split("[\n]", $bill);
		$cnt = count($table);
		if ($cnt == 0) {
			return false;
		}
		$header = split(",", $table[0]);
		$result = array();
		$result['header'] = $header;
		$result['rows'] = array();
		//		dump($header);
		for ($i = 1; $i < $cnt - 2; $i++) {
			$row = split(",", $table[$i]);
			//			dump($row);
			$result['rows'][] = $row;
		}
		if ($cnt - 2 > 0) {
			$footer_title = split(",", $table[$cnt - 2]);
			$footer_cont = split(",", $table[$cnt - 1]);
			//			dump($footer_title);
			//			dump($footer_cont);
			$result['footer'] = array("title" => $footer_title, "cont" => $footer_cont);
		}
		return $result;
	}
	
	private function check($orderid){
		$map = array("orderid"=>$orderid,"wxaccount"=>getWxAccountID());
		$result = apiCall("Admin/Orders/getInfo", array($map));
		if($result['status'] && is_array($result['info'])){
			return true;
		}else{
			return false;
		}
	}

}
