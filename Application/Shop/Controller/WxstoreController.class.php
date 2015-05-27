<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

class WxstoreController extends ShopController{
	
	
	/**
	 * 店铺查看
	 */
	public function view(){
		
		$storeid = I('id',0);
		$map = array(
			'id'=>$storeid
		);
		$result = apiCall("Shop/Wxstore/getInfo", array($map));
		if(!$result['status']){
			$this->error($result['info']);
		}
		$rank = convert2LevelImg($result['info']['exp']);
		$this->assign("vo",$result['info']);
		$this->assign("rank",$rank);
		
		
		
		$this->assign("products",$this->listProduts());
		
		$this->display();
	}
	
	public function listProduts(){
		$store_id = I('id',0);
		$p = I('post.p',0);
		
		$map = array();
		$map['storeid'] = $store_id;		
		//TODO: 商品展示要进行分页处理
		$page  = array('curpage'=>$p,'size'=>100);
		$order = " price desc";
		$result = apiCall("Shop/Wxproduct/query", array($map,$page,$order));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$list = $result['info']['list'];
		if(!is_null($list)){
			$list = $this -> queryMonthlySales($list);
		}
		
		return $list;
	}
	
	
	/**
	 * TODO: 查看所有的宝贝分类
	 */
	public function allCategory(){
		
		$this->redirect("Shop/Wxstore/view", array('id' => I('id')));
//		$this->error("TODO:查看所有的宝贝分类");
//		$this->display();
	}
	
	/**
	 * 搜索店铺
	 */
	public function search(){
		$q = I('q','');
		$cate_id = I('cate_id','');
		if(IS_POST){
			$cate_id = I('post.cate_id','');
		}
		
		if(IS_AJAX){
			
			//分页时带参数get参数
				
			$map = array();
			if(!empty($q)){
				$map['name'] = array('like',"%".$q."%");
			}
			if(!empty($cate_id)){
				$map['cate_id'] = $cate_id;
			}
			
			$page = array('curpage' => I('post.p', 0), 'size' => 10);
			$order = " createtime desc ";
			
			
			$result = apiCall('Admin/Wxstore/query', array($map, $page, $order));
			
			if ($result['status']) {
				$this->success($result['info']['list']);
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error(L('UNKNOWN_ERR'));
			}
		
		}else{
			$map = array('parentid'=>C('DATATREE.STORE_TYPE'));
	
			$result = apiCall('Admin/Datatree/queryNoPaging', array($map));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			
			$this->assign("q",$q);
			$this->assign("cates",$result['info']);			
			$this->assign("cate_id",$cate_id);
			
			$this->display();
		}
	
		
		
	}
	
	/**
	 * 获取多个商品的月销量
	 */
	private function queryMonthlySales($list) {
		$tmp_arr = array();
		foreach ($list as $vo) {
			array_push($tmp_arr, $vo['id']);
		}
		
		$result = apiCall("Shop/Orders/monthlySales", array($tmp_arr));
		
		if (!$result['status']) {
			$this -> error($result['info']);
		}

		$tmp_arr = null;
		$tmp_arr = array();
		$sales = $result['info'];
		foreach ($sales as $vo) {
			$tmp_arr[$vo['p_id']] = intval($vo['sales']);
		}
		
		foreach ($list as &$vo) {
			$id = intval($vo['id']);
			if (isset($tmp_arr[$id])) {
				$vo['_sales'] = $tmp_arr[$vo['id']];
			} else {
				$vo['_sales'] = 0;
			}
		}

		return $list;
	}
}

