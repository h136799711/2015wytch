<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use Common\Api\Api;
use Common\Model\WxproductSkuModel;


class WxproductSkuApi extends Api{
	
	protected function _init(){
		$this->model = new WxproductSkuModel();
	}
	
	/**
	 * 保存sku数据
	 */
	public function addSkuList($id,$sku_info,$list){
		$this->model->startTrans();
		$sql = "";
		$flag = true;
		$error = "";
		$map = array('product_id'=>$id);
		
		$result = $this->model->where($map)->delete();
		if($result === false){
			$flag = false;
			$error = $this->model->getDbError();
		}
		
		foreach($list as $vo){
			$entity = array( 
				'product_id'=>$id,
				'sku_id'=>$vo['sku_id'],
				'ori_price'=>$vo['ori_price']*100,
				'price'=>$vo['price']*100,
				'quantity'=>$vo['quantity'],
				'product_code'=>$vo['product_code'],
				'icon_url'=>$vo['icon_url'],
				'sku_desc'=>$vo['sku_desc'],
			);
			
			if($this->model->create($entity,1)){
				$result = $this->model->add();	
				if($result === false){
					$flag = false;
					$error = $this->model->getError();
				}			
			}else{
				$flag = false;
				$error = $this->model->getError();
			}	
			
		}
		
		
		if($flag){
			//更新 产品信息
			$entity = array(
				'has_sku'=>1,
				'sku_info'=>json_encode($sku_info,JSON_UNESCAPED_UNICODE),
			);
			$map = array('id'=>$id);
			$model = new \Common\Model\WxproductModel();
			$result = $model->where($map)->save($entity);
			if($result === false){
				$flag = false;
				$error = $this->model->getDbError();
			}
		}
		
		
		if($flag){
			$this->model->commit();
			return $this->apiReturnSuc($result);
		}else{
			$this->model->rollback();
			return $this->apiReturnErr($error);
		}
		
	}
		
}
