<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

//$EXPRESS = array('sf'=>"顺丰",'sto'=>"申通",'yt'=>"圆通",'yd'=>"韵达",'tt'=>"天天",'ems'=>"EMS",'zto'=>"中通",'ht'=>"汇通");

/**
 * 快递公司数据
 */
function getAllExpress(){
	$EXPRESS = C('express');
	return array(
		array('code'=>'sf','name'=>$EXPRESS['sf']),
		array('code'=>'sto','name'=>$EXPRESS['sto']),
		array('code'=>'yt','name'=>$EXPRESS['yt']),
		array('code'=>'yd','name'=>$EXPRESS['yd']),
		array('code'=>'tt','name'=>$EXPRESS['tt']),
		array('code'=>'ems','name'=>$EXPRESS['ems']),
		array('code'=>'zto','name'=>$EXPRESS['zto']),
		array('code'=>'ht','name'=>$EXPRESS['ht']),
	);
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
