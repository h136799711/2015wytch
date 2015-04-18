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
//		$prev_pro_time = S('TASK_PROCESS_TIME');
//		if($prev_pro_time === false){
//			S('TASK_PROCESS_TIME',time(),20*60);
//		}else{
//			exit("CACHE_PROCESS");
//		}
//		if(!($key === C('TASK_KEY'))){
//			echo "error";
//			exit();
//		}
	}
	
	/**
	 * 订单自动处理
	 */
	public function process(){
		ignore_user_abort(true); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
		static $flag = true;
		static $elapseTime = 0;
		$flag  = true;
		$elapseTime = 0;
//		ob_clean();
		while($flag){
			addWeixinLog($elapseTime,"任务处理开始");
//			$this->toRecieved();
//			$this->toCompleted();
//			$this->toCancel();
//			$this->category1();
			sleep(10);
			if($elapseTime > 1){
				$flag = false;
//				ob_flush();
			}
			$elapseTime++;
		}
	}
	
	
	public function props(){
		ignore_user_abort(true); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
		$appid = 'wx3fe04f32017f50a5';
		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
		$appid = 'wx58aea38c0796394d';
		$appsecret = '3e1404c970566df55d7314ecfe9ff437';
		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
		
		$cateApi = new \Tool\Api\CategoryApi();
		
		$list = array();
		$level = 3;
		$map = array('level'=>$level);
		$result = $cateApi->queryNoPaging($map);
		
		if(is_array($result['info'])){
			dump($result['info']);
			foreach($result['info'] as $vo){
				$prop = $wxapi->cateAllProp($vo['cate_id']);
				dump($prop);
				exit();
				if($prop['status']){
											
					foreach($prop['info'] as $vo2){
//						array_push($list,array('cate_id'=>$vo['id'],'propname'=>$vo2->name,'parent'=>$vo['id'],'propid'=>$vo2->id));
						$result2 = apiCall("Admin/CategoryProp/getInfo",array(array('propid'=>$vo2->id,'cate_id'=>$vo['cate_id'])));
						unset($list); 
						$list = array();
						if($result2['status'] && is_array($result2['info'])){
							foreach($vo2->property_value as $prop2){
								array_push($list,array('propvalueid'=>$prop2->id,'valuename'=>$prop2->name,'prop_id'=>$result2['info']['id']));
							}
							apiCall("Admin/CategoryPropvalue/addAll",array($list));
						}
					}
					
					
				}else{
					dump($prop['info']);
				}
			}
		}else{
			dump($result['info']);
		}
		
		
	}
	
	
	public function propvalue($propvalue){
		ignore_user_abort(true); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
		$appid = 'wx3fe04f32017f50a5';
		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
		
		$cateApi = new \Admin\Api\CategoryPropvalueApi();
		
		$list = array();
		$level = 3;
		$map = array();
		$result = $cateApi->queryNoPaging($map);
		
		if(is_array($result['info'])){
			dump($result['info']);
			foreach($result['info'] as $vo){
				$prop = $wxapi->cateAllProp($vo['cate_id']);
				unset($list); 
				$list = array();
				if($prop['status']){
											
					foreach($prop['info'] as $vo2){
						array_push($list,array('cate_id'=>$vo['id'],'propname'=>$vo2->name,'parent'=>$vo['id'],'propid'=>$vo2->id));
					}
					
					$result = apiCall("Admin/CategoryProp/addAll",array($list));
					
				}else{
//					dump($prop['info']);
				}
			}
		}else{
			dump($result['info']);
		}
		
		
	}
	
	public function category1(){
//		$this->category0();
		$appid = 'wx3fe04f32017f50a5';
		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
		$category = ($result['info']);
		$cateApi = new \Tool\Api\CategoryApi();
		$list = array();
		$level = 2;
		$map = array('level'=>$level);
		$result = $cateApi->queryNoPaging($map);
		
		if(is_array($result['info'])){
			dump($result['info']);
			foreach($result['info'] as $vo){
				$category = $wxapi->category($vo['cate_id']);
				unset($list); 
				$list = array();
				if($category['status']){
											
					foreach($category['info'] as $vo2){
						array_push($list,array('cate_id'=>$vo2->id,'name'=>$vo2->name,'parent'=>$vo['id'],'level'=>$level+1));
					}
					
					$result = $cateApi->addAll($list);
					
				}else{
					dump($category['info']);
				}
			}
		}else{
			dump($result['info']);
		}
		
		
	}
	
	
	public function category0(){
		$appid = 'wx3fe04f32017f50a5';
		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
		$cateApi = new \Tool\Api\CategoryApi();
		$list = array();
		$map = array('level'=>0);
		$result = $wxapi->category(1);
		dump($result);
		if(($result['status'])){
			foreach($result['info'] as $vo){
				array_push($list,array('cate_id'=>$vo->id,'name'=>$vo->name,'parent'=>1,'level'=>0));
			}
		}
		
		$result = $cateApi->addAll($list);
		
		
		addWeixinLog($result);
		
		
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
