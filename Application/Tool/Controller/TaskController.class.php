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
		//20分钟以内的请求只处理一次
		$prev_pro_time = S('TASK_PROCESS_TIME');
		if($prev_pro_time === false){
			S('TASK_PROCESS_TIME',time(),20*60);
		}else{
			echo "Cached-Time: ". date("Y-m-d H:i:s",$prev_pro_time);
			//缓冲处理
//			exit();
		}
		
		$this->getConfig();
	}
	
	/**
	 * 任务自动处理\异步
	 */
	public function index(){
		
		$url = C('SITE_URL').'/index.php/Tool/Task/aysnc';
//		echo $url;
		$result = fsockopenRequest($url,array('user'=>'www.itboye.com'),"POST");
		echo "Accept Request!";
		echo $result;
	}
	
	/**
	 * 任务处理区域
	 */
	public function aysnc(){
		$user = I('post.user','');
		if($user != "www.itboye.com"){
			addWeixinLog(get_client_ip(0,true),"非法用户访问");
			return ;
		}
		
		addWeixinLog(get_client_ip(0,true),"合法用户");
		ignore_user_abort(true); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
//		
		$this->toRecieved();
		$this->toCompleted();
		$this->toCancel();
	}
	
	
	/**
	 * 
	 * 1. 订单[取消]－》检测 time() - updatetime > 指定时间，暂定1小时 满足条件变更为订单[取消]
	 */
	private function toCancel(){
		
		$interval = 3600*1;//1小时
		$result = apiCall("Tool/Orders/orderStatusToCancel",array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			if($result['info'] > 0){
				addWeixinLog("更新订单为取消影响记录数：".$result['info'],'0');
			}
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
			if($result['info'] > 0){
				addWeixinLog("更新订单为已收货影响记录数：".$result['info'],'0');
			}
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
			if($result['info'] > 0){
				addWeixinLog("更新订单为已完成影响记录数：".$result['info'],'3');
			}
		}
	}
	
	
	/**
	 * 从数据库中取得配置信息
	 */
	protected function getConfig() {
		$config = S('global_tool_config');

		if ($config === false) {
			$map = array();
			$fields = 'type,name,value';
			$result = apiCall('Admin/Config/queryNoPaging', array($map, false, $fields));
			if ($result['status']) {
				$config = array();
				if (is_array($result['info'])) {
					foreach ($result['info'] as $value) {
						$config[$value['name']] = $this -> parse($value['type'], $value['value']);
					}
				}
				//缓存配置300秒
				S("global_tool_config", $config, 2400*1);
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		}
		C($config);
	}

	/**
	 * 根据配置类型解析配置
	 * @param  integer $type  配置类型
	 * @param  string  $value 配置值
	 */
	private static function parse($type, $value) {
		switch ($type) {
			case 3 :
				//解析数组
				$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
				if (strpos($value, ':')) {
					$value = array();
					foreach ($array as $val) {
						list($k, $v) = explode(':', $val);
						$value[$k] = $v;
					}
				} else {
					$value = $array;
				}
				break;
		}
		return $value;
	}
	
	
	
	/**
	 * 订单自动处理
	 */
//	public function process(){
//		ignore_user_abort(true); // 后台运行
//		set_time_limit(0); // 取消脚本运行时间的超时上限
//		static $flag = true;
//		static $elapseTime = 0;
//		$flag  = true;
//		$elapseTime = 0;
////		ob_clean();
//		while($flag){
////			addWeixinLog($elapseTime,"任务处理开始");
//			$this->toRecieved();
//			$this->toCompleted();
//			$this->toCancel();
////			$this->category1();
//			sleep(10);
//			if($elapseTime > 1){
//				$flag = false;
////				ob_flush();
//			}
//			$elapseTime++;
//		}
//	}
	
	
//	public function insertsku(){
//		ignore_user_abort(true); // 后台运行
//		set_time_limit(0); // 取消脚本运行时间的超时上限
//		$appid = 'wx3fe04f32017f50a5';
//		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
////		$appid = 'wx58aea38c0796394d';
////		$appsecret = '3e1404c970566df55d7314ecfe9ff437';
//		
//		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
//		
//		$cateApi = new \Tool\Api\CategoryApi();
//		
//		$level = 2;
//		$map = array();
//		$map['id'] = array('gt',3195);
//		$result = $cateApi->queryNoPaging($map);
//		
//		$skuApi = new \Admin\Api\SkuApi();
//		$skuvalueApi = new \Admin\Api\SkuvalueApi();
//		if(is_array($result['info'])){
//			foreach($result['info'] as $vo){
//				$map['parent'] = $vo['id'];
//				$result2 = $cateApi->queryNoPaging($map);
//				if($result2['status']){
//					if(is_null($result2['info']) || count($result2['info']) == 0){
//						//处理
//						dump($vo['cate_id']);
//						dump($result2);
//						$sku_table = $wxapi->getSKU($vo['cate_id']);
//						if(!$sku_table['status']){
//							dump('getSKU'.$sku_table['info']);
//							continue;
//						}
//						$sku_table = $sku_table['info'];
////						dump($sku_table);
//						for($i=0;$i<count($sku_table);$i++){
//							$entity = array(
//								'cate_id'=>$vo['cate_id'],
//								'name'=>$sku_table[$i]->name,
//								'sku_id'=>$sku_table[$i]->id
//							);
//							dump($entity);
//							$result = $skuApi->add($entity);
//													
//							if($result['status']){
//							
//								$skuvaluelist = array();
//								foreach($sku_table[$i]->value_list as $skuvalue){
//									array_push($skuvaluelist,array('name'=>$skuvalue->name,'vid'=>$skuvalue->id,'sku_id'=>$result['info']));								
//								}
//								
//								$skuvalueApi->addAll($skuvaluelist);
//								
//							}else{
//								dump($result['info']);
//								exit();
//							}
//							
//						}//end for
////						exit();//第一次
//					}
//					
//				}else{
//					dump($result2['info']);
//					exit();
//				}
//			}
//			
//		}else{
//			dump($result['info']);	
//		}
//
//	}
	
//	public function props(){
//		ignore_user_abort(true); // 后台运行
//		set_time_limit(0); // 取消脚本运行时间的超时上限
//		$appid = 'wx3fe04f32017f50a5';
//		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
////		$appid = 'wx58aea38c0796394d';
////		$appsecret = '3e1404c970566df55d7314ecfe9ff437';
//		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
//		
//		$cateApi = new \Tool\Api\CategoryApi();
//		
//		$list = array();
//		$level = 2;
//		$map = array('level'=>$level);
//		$result = $cateApi->queryNoPaging($map);
//		//49290
//		if(is_array($result['info'])){
////			dump($result['info']);
//			foreach($result['info'] as $vo){
//				$prop = $wxapi->cateAllProp($vo['cate_id']);
////				$prop = $wxapi->cateAllProp('537091432');
//			
////				dump($prop);
////				exit();
//				if($prop['status']){
//					
//					foreach($prop['info'] as $vo2){
////						array_push($list,array('cate_id'=>$vo['id'],'propname'=>$vo2->name,'parent'=>$vo['id'],'propid'=>$vo2->id));
//						$result2 = apiCall("Admin/CategoryProp/getInfo",array(array('propid'=>$vo2->id,'cate_id'=>$vo['id'])));
////						$result2 = apiCall("Admin/CategoryProp/getInfo",array(array('propid'=>$vo2->id,'cate_id'=>'537091432')));
////						dump($result2);
////						dump($result2);
//						unset($list); 
//						$list = array();
//						if($result2['status'] && is_array($result2['info'])){
//							foreach($vo2->property_value as $prop2){
//								array_push($list,array('propvalueid'=>$prop2->id,'valuename'=>$prop2->name,'prop_id'=>$result2['info']['id']));
//							}	
//							apiCall("Admin/CategoryPropvalue/addAll",array($list));
//							
//						}else{
//							
//						}
////						dump($list);
//					}
////exit();
//					
//				}else{
//					if(!(strpos($prop['info'],"接口调用") === FALSe )){
//						dump($prop['info']);
//						exit();
//					}
//				}
//			}
//		}else{
//			dump($result['info']);
//		}
//		
//		
//	}
	
	
//	public function propvalue($propvalue){
//		ignore_user_abort(true); // 后台运行
//		set_time_limit(0); // 取消脚本运行时间的超时上限
//		$appid = 'wx3fe04f32017f50a5';
//		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
//		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
//		
//		$cateApi = new \Admin\Api\CategoryPropvalueApi();
//		
//		$list = array();
//		$level = 3;
//		$map = array();
//		$result = $cateApi->queryNoPaging($map);
//		
//		if(is_array($result['info'])){
//			dump($result['info']);
//			foreach($result['info'] as $vo){
//				$prop = $wxapi->cateAllProp($vo['cate_id']);
//				unset($list); 
//				$list = array();
//				if($prop['status']){
//											
//					foreach($prop['info'] as $vo2){
//						array_push($list,array('cate_id'=>$vo['id'],'propname'=>$vo2->name,'parent'=>$vo['id'],'propid'=>$vo2->id));
//					}
//					
//					$result = apiCall("Admin/CategoryProp/addAll",array($list));
//					
//				}else{
////					dump($prop['info']);
//				}
//			}
//		}else{
//			dump($result['info']);
//		}
//		
//		
//	}
	
//	public function category1(){
////		$this->category0();
//		$appid = 'wx3fe04f32017f50a5';
//		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
//		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
//		$category = ($result['info']);
//		$cateApi = new \Tool\Api\CategoryApi();
//		$list = array();
//		$level = 2;
//		$map = array('level'=>$level);
//		$result = $cateApi->queryNoPaging($map);
//		
//		if(is_array($result['info'])){
//			dump($result['info']);
//			foreach($result['info'] as $vo){
//				$category = $wxapi->category($vo['cate_id']);
//				unset($list); 
//				$list = array();
//				if($category['status']){
//											
//					foreach($category['info'] as $vo2){
//						array_push($list,array('cate_id'=>$vo2->id,'name'=>$vo2->name,'parent'=>$vo['id'],'level'=>$level+1));
//					}
//					
//					$result = $cateApi->addAll($list);
//					
//				}else{
//					dump($category['info']);
//				}
//			}
//		}else{
//			dump($result['info']);
//		}
//		
//		
//	}
//	
//	
//	public function category0(){
//		$appid = 'wx3fe04f32017f50a5';
//		$appsecret = 'f7dbb6d7882ecaa984a9f3e900db9a3d';
//		$wxapi = new \Common\Api\WxShopApi($appid,$appsecret);
//		$cateApi = new \Tool\Api\CategoryApi();
//		$list = array();
//		$map = array('level'=>0);
//		$result = $wxapi->category(1);
//		dump($result);
//		if(($result['status'])){
//			foreach($result['info'] as $vo){
//				array_push($list,array('cate_id'=>$vo->id,'name'=>$vo->name,'parent'=>1,'level'=>0));
//			}
//		}
//		
//		$result = $cateApi->addAll($list);
//		
//		
//		addWeixinLog($result);
//		
//		
//	}
	
	
	
}
