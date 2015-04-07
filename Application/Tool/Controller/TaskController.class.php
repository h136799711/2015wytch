<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Controller;
use Think\Controller;

/**
 * 任务运行
 */
class TaskController extends Controller{
	
	protected function _initialize(){
//		$key = I('get.key','');

		//20分钟
		$prev_pro_time = S('TASK_PROCESS_TIME');
		if($prev_pro_time === false){
			S('TASK_PROCESS_TIME',time(),20*60);
		}else{
			exit("CACHE_PROCESS");
		}
//		if(!($key === C('TASK_KEY'))){
//			echo "error";
//			exit();
//		}
	}
	
	/**
	 * 订单自动处理
	 */
	public function process(){
		$this->toRecieved();
		$this->toCompleted();
		$this->toCancel();
	}
	
	/**
	 * 
	 * 1. 订单[取消]－》检测 time() - updatetime > 指定时间，暂定1天 满足条件变更为订单[取消]
	 */
	private function toCancel(){
		$interval = 24*3600*1;//30天
		$result = apiCall("Tool/Orders/orderStatusToCancel",array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			addWeixinLog("更新订单为取消影响记录数：".$result['info']);
		}
	}
	
	
	/**
	 * 
	 * 1. 订单[已发货]－》检测 time() - updatetime > 指定时间，暂定30天 满足条件变更为订单[已发货]
	 */
	private function toRecieved(){
		$interval = 24*3600*30;//30天
		$result = apiCall("Tool/Orders/orderStatusToRecieved",array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			addWeixinLog("更新订单为已收货影响记录数：".$result['info']);
		}
	}
	
	/**
	 * 
	 * 1. 订单[已收货]－》检测 time() - updatetime > 指定时间，暂定15天 满足条件变更为订单[已收货]
	 */
	private function toCompleted(){
		$interval = 24*3600*15;//15天
		$result = apiCall("Tool/Orders/orderStatusToCompleted",array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			addWeixinLog("更新订单为已完成影响记录数：".$result['info']);
		}
	}
	
	
	
}
