<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use \Common\Api\Api;
use \Common\Model\WxproductModel;

class WxproductApi extends Api{
	protected function _init(){
		$this->model = new WxproductModel();
	}
	
	public function queryWithStoreUID($uid=0,$map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false){
		$query = $this->model;
		
		$result = $query->query("select * from __WXSTORE__ where uid = $uid");
		
		if($result === false){
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}
		$storeidlist = array('0');
		foreach($result as $store){
			array_push($storeidlist,($store['id']));
		}
		
		$_map['storeid'] = array('in',$storeidlist);
		
		$map = array_merge($_map,$map);
		
		if(!is_null($map)){
			$query = $query->where($map);
		}
		
		if(!($order === false)){
			$query = $query->order($order);
		}
		if(!($fields === false)){
			$query = $query->field($fields);
		}
		$list = $query -> page($page['curpage'] . ',' . $page['size'])  -> select();
		

		if ($list === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}

		$count = $this -> model -> where($map) -> count();
		// 查询满足要求的总记录数
		$Page = new \Think\Page($count, $page['size']);

		//分页跳转的时候保证查询条件
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

