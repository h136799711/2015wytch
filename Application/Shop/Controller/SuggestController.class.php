<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

class SuggestController extends ShopController{
	
	
	/**
	 * 帮助与意见
	 */
	public function add(){
		if(IS_GET){		
			$this->display();
		}else{
			$name = I("post.name","");
			$phone = I("post.phone","");
			$text = I("post.text","");
			
			
			
			if(empty($text)){
				$this->error("意见或建议内容必须填写!");
			}
			$entity = array(
				'suggestion'=>$text,
				'text'=>'',
				'process_status'=>'',
				'name'=>$name,
				'tel'=>$phone,
			);
			
			$result = apiCall("Shop/Suggest/add", array($entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("感谢您的意见或建议!",U('Shop/User/info'));
		}
	}
}

