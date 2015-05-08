<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Shop\Controller;

class OrderCommentController extends ShopController{
	
	public function index(){
		if(IS_POST){
			$productid = I('post.productid',0);
			
			$map = array('product_id'=>$productid);
			$page = array('curpage'=>I('post.p',0),'size'=>20);
			$order = " createtime desc ";
			$result = apiCall("Shop/OrderComment/query",array($map,$page,$order));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$result_info = $result['info']['list'];
			
			$user_ids = array();
			if(is_array($result['info']['list'])){
				foreach($result['info']['list'] as $vo){
					array_push($user_ids, $vo['user_id']);
				}
			}
			$user_ids = array_unique($user_ids);
			
			if(count($user_ids) > 0){
				
				$result = apiCall("Shop/Wxuser/queryNoPaging",array($map));
				
				if(!$result['status']){
					$this->error($result['info']);
				}
				$user_info = array();
				foreach($result['info'] as $vo){
					$user_info[$vo['id']] = $vo;
				}
				
				foreach($result_info as &$vo){
					$vo['_createtime'] = date("Y.m.d H.i.s",$vo['createtime']);
					$str = $user_info[$vo['user_id']]['nickname'];
					
					$vo['_encrypt_nickname'] = mb_substr($str,0,1,'UTF-8').'**'.mb_substr($str,mb_strlen($str,'UTF-8')-1,1,'UTF-8');
					$vo['_userlogo'] =str_replace("/0", "/64",$user_info[$vo['user_id']]['avatar']);
//					unset($user_info[$vo['user_id']['nickname']]);
					$vo['_userinfo'] = $user_info[$vo['user_id']];
				}
			}
			
//			dump($result_info);
//			echo json_encode($result_info);
			
			$this->success($result_info);
		}
	}

	
	
}
