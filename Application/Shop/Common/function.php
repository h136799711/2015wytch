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
		array_push($exp,array('code'=>$key,'name'=>$vo))
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
