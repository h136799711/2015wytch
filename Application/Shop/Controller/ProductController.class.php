<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

class ProductController extends ShopController {

	public function quickSort($left, $right, $arr) {
		$l = $left;
		$r = $right;
		$pivot = $arr[($left + $right) / 2];
		$temp = 0;

		while ($l < $r) {
			
//			call_user_func_array($func_callback, array());
			while (($arr[$l]['_sales']) > ($pivot['_sales'])) {
				$l++;
			}
			while (($arr[$r]['_sales']) < ($pivot['_sales'])) {
				$r--;
			}

			if ($l >= $r)
				break;
			$temp = $arr[$l];
			$arr[$l] = $arr[$r];
			$arr[$r] = $temp;
			
			if ($arr[$l]['_sales'] == $pivot['_sales'])
				--$r;
			if ($arr[$r]['_sales'] == $pivot['_sales'])
				++$l;
		}
		
		if ($l == $r) {
			$l++;
			$r--;
		}
		if ($left < $r) {
			return $this->quickSort($left, $r, $arr);
		} elseif ($right > $l) {
			return $this->quickSort($l, $right, $arr);
		} else {
			return $arr;
		}
	}

	/**
	 * 发现
	 */
	public function search() {
		//排序： s 综合 ，d 销量 ,p 价格 从小到大, pd 价格 从大到小
		$sort = I('sort', 's');
		$type = I('type', '1');
		$layout = I('get.layout', 'list');
		
		$map = array();
		$q = I('q','');
		$page = array('curpage' => I('post.p', 0,'intval'), 'size' => 10);
		$order = " id desc ";
		if ($sort == 's') {
			$order = " price desc";
		}
		if ($sort == 'p') {
			$order = " price desc";
		}
		if ($sort == 'pd') {
			$order = " price asc";
		}
		
		
		$params = false;
		$result = apiCall("Shop/Wxproduct/queryWithWxstore", array($q,$type, $page, $order, $params));
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$list = $result['info']['list'];
		if(!is_null($list)){
			$list = $this -> queryMonthlySales($list);
//			dump($list);
			if ($sort == 'd') {
				//对销量进行排序
				$list = ($this->quickSort(0, count($list)-1,  $list));
				
			}
		}
		
		if(IS_POST){
//			echo json_encode($list);
			$this->success($list);
		}else{
			$this -> assign("q", $q);
			$this -> assign("layout", $layout);
			$this -> assign("sort", $sort);
			$this -> assign("curpage", $page['curpage']);
			$this -> assign("show", $result['info']['show']);
			$this -> assign("list", $list);
			$this -> display();
		}
	}
	/**
	 * 商品分组页面
	 */
	public function group() {
		if(IS_GET){
			$groupid = I('get.id',0);
			$this->assign("groupid",$groupid);
			$this->display();
			
		}elseif(IS_AJAX){
			$p = I('post.p',0);
			
			$page = array('curpage'=>$p,'size'=>10);
			$order = " updatetime desc";
			$map = array('onshelf'=>\Common\Model\WxproductModel::STATUS_ONSHELF);
			$group_id = I('post.groupid',0);
			
			$result = apiCall("Shop/Wxproduct/queryByGroup", array($group_id,$map,$page));
			if(!$result['status']){
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);	
			}
			
			$products = $result['info']['list'];
			
			foreach($products as &$vo){
				
				$vo['_zk_percent'] =  number_format(($vo['price']/$vo['ori_price'])*10.0,2);
				$vo['price'] =  number_format($vo['price']/100.0,2);
				$vo['ori_price'] =  number_format($vo['ori_price']/100.0,2);
				
			}
			
			$this->success($products);
			
		}
	}

	/**
	 * 商品详情查看
	 */
	public function detail() {
		if (IS_GET) {
			$id = I('get.id', 0);
			$result = apiCall("Admin/Wxproduct/getInfo", array( array('id' => $id)));

			if (!$result['status']) {
				$this -> error($result['info']);
			}
			$banners = $this -> getBanners($result['info']);
			if ($result['info']['attrext_ispostfree'] == '0') {
				$uni_express = json_decode($result['info']['express']);
				//				{"id":10000027,"price":1000},{"id":10000028,"price":1000},{"id":10000029,"price":1000}]

				$express_str = "";
				foreach ($uni_express as $vo) {
					if (($vo -> id == 10000027)) {
						$express_str .= "平邮: " . number_format($vo -> price / 100.0, 1);
					} elseif ($vo -> id == 10000028) {
						$express_str .= "快递: " . number_format($vo -> price / 100.0, 1);
					} elseif ($vo -> id == 10000029) {
						$express_str .= "EMS: " . number_format($vo -> price / 100.0, 1);
					}
				}
				//				if($uni_expr)

				$this -> assign("express_str", $express_str);
			}

			if ($result['info']['has_sku'] == '1') {
				$skulist = $this -> getSkuList($result['info']);
				//				dump($skulist);
				//				exit();
				$this -> assign("sku_arr", $skulist['sku_arr']);
				$this -> assign("sku_list", json_encode($skulist['sku_list']));
			} else {
				$this -> assign("sku_list", json_encode(array()));
			}
			$this -> assign("properties", $this -> getProperties($result['info']['properties']));
			$details = htmlspecialchars_decode($result['info']['detail']);
			$this -> assign("details", json_decode($details));
			$this -> assign("banners", $banners);
			$this -> assign("product", $result['info']);
			$result = apiCall("Admin/Wxstore/getInfo", array( array('id' => $result['info']['storeid'])));
			
			if ($result['status']) {
				$this -> assign("wxstore", $result['info']);
			}
			
			$monthlySales = $this -> getMonthlySales($id);
			//获取月销量
			$this -> assign("monthlySales", $monthlySales);
			
			$this -> display();

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

	/**
	 * 获取单个商品月销量
	 */
	private function getMonthlySales($p_id) {
//		dump($p_id);
		//统计订单数 ， 满足条件小于当前时间，大于当前时间－30天
		$result = apiCall("Shop/OrdersItem/monthlySales", array($p_id));
		if (!$result['status']) {
			$this -> error($result['info']);
		}

		return $result['info'];
	}

	private function getSkuList($product) {
		$skuinfo = json_decode($product['sku_info']);
		$sku_ids = array('-1');
		$sku_value_ids = array('-1');
		foreach ($skuinfo as $vo) {
			array_push($sku_ids, $vo -> id);
			foreach ($vo->vid as $vid) {
				array_push($sku_value_ids, $vid);
			}
		}

		$map = array();
		$map['id'] = array('in', $sku_ids);

		$result = apiCall("Admin/Sku/queryNoPaging", array($map));
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$sku_result = $result['info'];

		$map = array();
		$map['id'] = array('in', $sku_value_ids);

		$result = apiCall("Admin/Skuvalue/queryNoPaging", array($map));
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$sku_value_result = $result['info'];
		//上述代码获取SKU以及SKU值的名称

		$sku_arr = array();
		foreach ($sku_result as $_sku) {
			$key = $_sku['id'] . ':';
			foreach ($sku_value_result as $_sku_value) {
				if ($_sku_value['sku_id'] == $_sku['id']) {
					//						$tmpKey = $key . $_sku_value['id'];
					//						$sku_arr[$tmpKey] = array('sku_id'=>$_sku['id'],'sku_name'=>$_sku['name'],'sku_value_name'=>$_sku_value['name']);
					if (!isset($sku_arr[$_sku['id']])) {
						$sku_arr[$_sku['id']] = array('id' => $_sku['id'], 'sku_name' => $_sku['name'], 'sku_value_list' => array());
					}

					array_push($sku_arr[$_sku['id']]['sku_value_list'], array('id' => $_sku_value['id'], 'name' => $_sku_value['name']));

				}
			}
		}

		$result = apiCall("Admin/WxproductSku/queryNoPaging", array( array('product_id' => $product['id'])));
		if (!$result['status']) {
			$this -> error($result['info']);
		}

		$formatSku = array();
		foreach ($result['info'] as &$vo) {
			$formatSku[$vo['sku_id']] = array('icon_url' => $vo['icon_url'], 'ori_price' => $vo['ori_price'], 'price' => $vo['price'], 'product_code' => $vo['product_code'], 'product_id' => $vo['product_id'], 'quantity' => $vo['quantity'], );
		}
		return array('sku_list' => $formatSku, 'sku_arr' => $sku_arr);
	}

	/**
	 * 获取属性对应的文字描述
	 */
	private function getProperties($prop) {
		if (empty($prop)) {
			return array();
		}
		$prop_arr = explode(";", $prop);
		$prop_ids = array();
		$propvalue_ids = array();
		$result = array();
		foreach ($prop_arr as $vo) {
			if ($vo) {
				$prop_value = explode(",", $vo);
				array_push($prop_ids, $prop_value[0]);
				array_push($propvalue_ids, $prop_value[1]);
			}
		}

		$map = array();
		$map['id'] = array("in", $prop_ids);
		$order =  " id asc ";
		$prop_result = apiCall("Admin/CategoryProp/queryNoPaging", array($map,$order));
		if (!$prop_result) {
			$this -> error($prop_result['info']);
		}
		
		$prop_result = $prop_result['info'];
		
		$map = array();
		$map['id'] = array("in", $propvalue_ids);
		$order =  " prop_id asc ";
		$propvalue_result = apiCall("Admin/CategoryPropvalue/queryNoPaging", array($map,$order));
		if (!$propvalue_result) {
			$this -> error($propvalue_result['info']);
		}
		
		$propvalue_result = $propvalue_result['info'];
		for ($i = 0; $i < count($prop_result); $i++) {
			$p = $prop_result[$i];
			$pv = $propvalue_result[$i];

			array_push($result, array('name' => $p['propname'], 'value' => $pv['valuename']));

		}
		return $result;
	}

	/**
	 * 从商品信息中提取图片
	 */
	private function getBanners($product) {

		$imgs = explode(",", $product['img']);
		array_pop($imgs);
		array_push($imgs, $product['main_img']);
		return $imgs;
	}

}
