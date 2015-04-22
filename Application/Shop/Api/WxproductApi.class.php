<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;

use Common\Api\Api;
use Common\Model\WxproductModel;

class WxproductApi extends Api{
	
	protected function _init(){
		$this->model = new WxproductModel();
	}
	
	public function queryByGroup($group_id,$map,$page){
		
		$result = $this->model->query("select * from __WXPRODUCT_GROUP__ where g_id = ".$group_id);
		if($result === FALSE){
			return $this->apiReturnErr($this->model->getDbError());
		}
		$product_ids = array();
		
		foreach($result as $vo){
			array_push($product_ids,$vo['p_id']);
		}
		
		if(is_null($map)){
			$map = array();
		}
		
		$map['id'] = array('in',$product_ids);
		
		
		$query = $this->model;
		if(!is_null($map)){
			$query = $query->where($map);
		}
		if(!($order === false)){
			$query = $query->order($order);
		}
		if(!($fields === false)){
			$query = $query->field($fields);
		}
		$list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();
		

		if ($list === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}

		$count = $this -> model -> where($map) -> count();
		// 查询满足要求的总记录数
		$Page = new \Think\Page($count, $page['size']);
		
		// 分页跳转的时候保证查询条件
		if ($params !== false) {
			foreach ($params as $key => $val) {
				$Page -> parameter[$key] = urlencode($val);
			}
		}

		// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page -> show();
		
		return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
		
	}
}

