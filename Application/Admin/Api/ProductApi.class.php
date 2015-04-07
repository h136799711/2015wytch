<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Admin\Api;
use Common\Model\ProductModel;

class ProductApi extends \Common\Api\Api{
	
	protected function _init(){
		$this->model = new ProductModel();
	}
  	
	public function getInfoWithThumbnail($id){
		$result = $this->model->alias("p")->field("p.id ,p.name,p.price,p.stock,p.sale_num,p.thumbnail,p.rate_num,p.detailtplid,p.detailcontent,p.dis_price,p.createtime,p.updatetime,p.wxaccountid,pic.path as thumbnaillocal,pic.url as thumbnailremote")->join('LEFT JOIN __PICTURE__ as pic ON pic.id = p.thumbnail')	
		->where(array('p.id'=>$id))->find();																																			
		
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}

		return $this -> apiReturnSuc($result);
	}
	
}
