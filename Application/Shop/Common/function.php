<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


/**
 * 快递公司数据
 */
function getAllExpress(){
	$EXPRESS = C('express');
	$exp = array();
	foreach($EXPRESS as $key=>$vo){
		array_push($exp,array('code'=>$key,'name'=>$vo));
	}
	
	return $exp;
}

function toYesOrNo($val){
	if($val == 1 || $val == true){
		return "是";
	}
	return "否";
}

/**
 * 获取提现记录
 */
function getWDCStatus($status){
	$desc = "未知";
	switch($status){
		case \Common\Model\CommissionWithdrawcashModel::WDC_STATUS_PENDING_AUDIT:
			$desc = "待审核";
			break;
		case \Common\Model\CommissionWithdrawcashModel::WDC_STATUS_APPROVAL:
			$desc = "已确认";
			break;
		case \Common\Model\CommissionWithdrawcashModel::WDC_STATUS_REJECT:
			$desc = "驳回";
			break;
		default:		;
	}
	
	return $desc;
}
/**
 * 转换等级为相应图片展示
 */
function convert2LevelImg($exp){
	$level = getStoreLevel($exp);
	$loop = 1;
	$return = "";
	$img = "";
	if($level < 10){
		$img = 1;
		$loop = $level - 0;
	}elseif($level < 20){
		$img = 2;
		$loop = $level - 10;
		
	}elseif($level < 30){
		$img =3;
		$loop = $level - 20;
		
	}else{
		$img = 100;
		$loop = 1;
		return "";
	}
	
	return array('type'=>$img,'loop'=>$loop);
	
}
/**
 * 获取店铺经验对应的等级
 */
function getStoreLevel($exp){
	//店铺经验
	//星星
	if($exp < 250){
		//星
		if($exp > 0 && $exp < 10){
			return 1;//1颗
		}elseif($exp > 10 && $exp < 40){
			return 2;//2颗星
		}elseif($exp > 40 && $exp < 90){
			return 3;//3颗星
		}elseif($exp > 90 && $exp < 150){
			return 4;//4颗星
		}elseif($exp > 150 && $exp < 250){
			return 5;//5颗星
		}
	}elseif($exp < 10000){
		//钻石
		if($exp > 250 && $exp < 500){
			return 11;//1颗
		}elseif($exp > 500 && $exp < 1000){
			return 12;//2颗
		}elseif($exp > 1000 && $exp < 2000){
			return 13;//3颗
		}elseif($exp > 2000 && $exp < 5000){
			return 14;//4颗
		}elseif($exp > 5000 && $exp < 10000){
			return 15;//5颗
		}
		
	}elseif($exp < 500000){
		//金色皇冠
		if($exp > 250 && $exp < 500){
			return 21;//1颗
		}elseif($exp > 500 && $exp < 1000){
			return 22;//2颗
		}elseif($exp > 1000 && $exp < 2000){
			return 23;//3颗
		}elseif($exp > 2000 && $exp < 5000){
			return 24;//4颗
		}elseif($exp > 5000 && $exp < 10000){
			return 25;//5颗
		}
	}else{
		return 100;//最高等级
	}
	
	
}


/**
 * 获取订单状态的文字描述
 */
function getTaobaoOrderStatus($status) {
	
	switch($status) {
		case \Common\Model\OrdersModel::ORDER_COMPLETED :
			return "交易成功";
		case \Common\Model\OrdersModel::ORDER_RETURNED :
			return "已退货";
		case \Common\Model\OrdersModel::ORDER_SHIPPED :
			return "卖家已发货";
		case \Common\Model\OrdersModel::ORDER_TOBE_CONFIRMED :
			return "等待卖家确认";
		case \Common\Model\OrdersModel::ORDER_TOBE_SHIPPED :
			return "等待卖家发货";
		case \Common\Model\OrdersModel::ORDER_CANCEL :
			return "交易关闭";
		case \Common\Model\OrdersModel::ORDER_RECEIPT_OF_GOODS :
			return "买家已收货";
		case \Common\Model\OrdersModel::ORDER_BACK :
			return "卖家退回了该订单";
		default :
			return "未知";
	}
}