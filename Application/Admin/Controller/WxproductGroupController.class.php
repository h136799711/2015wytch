<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class WxproductGroupController extends AdminController{
		
	public function add(){
		$product_id = I('post.product_id',0);
		$groups = I('post.groups',array());
		if($product_id == 0){
			$this->error("参数错误！");
		}
		
		if(count($groups) > 0){
			foreach($groups as $groupid){
				$entity  = array(
					'p_id'=>$product_id,
					'g_id'=>$groupid,
				);
				
				$result = apiCall("Admin/WxproductGroup/getInfo", array($entity));
				if(!$result['status']){
					$this->error($result['info']);
				}

				if(is_null($result['info'])){
					$result = apiCall("Admin/WxproductGroup/add", array($entity));
					if(!$result['status']){
						$this->error($result['info']);
					}				
				}
				
				
			}
			array_push($groups,"-1");
			$map = array('g_id'=>array('not in',$groups));
			$map['p_id'] = $product_id;
			$result = apiCall("Admin/WxproductGroup/delete", array($map));
		
		}else{
			$result = array('status'=>true);
			$result = apiCall("Admin/WxproductGroup/delete", array(array('p_id'=>$product_id)));
		}
		
		if($result['status']){
			$this->success("操作成功！");
		}else{
			$this->error($result['info']);
		}
	}
	
}
