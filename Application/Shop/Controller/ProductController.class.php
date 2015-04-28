<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

class ProductController extends ShopController{
	
	public function test(){
		
		$map = array();
		$page = array('curpage'=>I('post.p',0),'size'=>20);
		if($sort == 's'){
			$order = " price desc , ";
		}
		$order = " id desc ";
		$params = false;
		$result = apiCall("Shop/Wxproduct/query", array($map,$page,$order,$params));
		
		echo json_encode($result['info']['list']);
		exit();
	}
	
	/**
	 * 发现
	 */
	public function random(){
		//排序： s 综合 ，d 销量 ,p 价格 从小到大, pd 价格 从大到小
		$sort = I('get.sort','s');
		
		$map = array();
		$page = array('curpage'=>I('post.p',0),'size'=>20);
		if($sort == 's'){
			$order = " price desc , ";
		}
		$order = " id desc ";
		$params = false;
		$result = apiCall("Shop/Wxproduct/query", array($map,$page,$order,$params));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->assign("curpage",$page['curpage']);
		$this->assign("show",$result['info']['show']);
		$this->assign("list",$result['info']['list']);
		$this->display();
	}
	
	/**
	 * 商品详情查看
	 */
	public function detail(){
		if(IS_GET){
			$id = I('get.id',0);
			$result = apiCall("Admin/Wxproduct/getInfo", array(array('id'=>$id)));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			$banners = $this->getBanners($result['info']);
			if($result['info']['attrext_ispostfree'] == '0'){
				$uni_express = json_decode($result['info']['express']);
//				{"id":10000027,"price":1000},{"id":10000028,"price":1000},{"id":10000029,"price":1000}]
				
				$express_str = "";
				foreach($uni_express as $vo){
					if(($vo->id == 10000027)){
						$express_str .= "平邮: ".number_format($vo->price / 100.0,1);
					}elseif($vo->id == 10000028){
						$express_str .= "快递: ".number_format($vo->price / 100.0,1);
					}elseif($vo->id == 10000029){
						$express_str .= "EMS: ".number_format($vo->price / 100.0,1);
					}					
				}
//				if($uni_expr)
				
				$this->assign("express_str",$express_str);
			}
			
			if($result['info']['has_sku'] == '1'){
				$skulist = $this->getSkuList($result['info']);
//				dump($skulist);
//				exit();
				$this->assign("sku_arr",$skulist['sku_arr']);
				$this->assign("sku_list",json_encode($skulist['sku_list']));
			}else{
				$this->assign("sku_list",json_encode(array()));
			}
			
			$this->assign("properties",$this->getProperties($result['info']['properties']));
			$details = htmlspecialchars_decode($result['info']['detail']);
			$this->assign("details",json_decode($details));
			$this->assign("banners",$banners);
			$this->assign("product",$result['info']);
			$result = apiCall("Admin/Wxstore/getInfo", array(array('id'=>$result['info']['storeid'])));
			
			if($result['status']){
//      dump($result['info']);
				$this->assign("wxstore",$result['info']);
			}
			
			$this->display();
			
		}
		
	}
	
	
	private function getSkuList($product){
		$skuinfo = json_decode($product['sku_info']);
		$sku_ids = array('-1');
		$sku_value_ids = array('-1');
		foreach($skuinfo as $vo){
			array_push($sku_ids, $vo->id);
			foreach($vo->vid as $vid){
				array_push($sku_value_ids, $vid);
			}
		}
		
		
		$map = array();
		$map['id'] = array('in',$sku_ids);
		
		$result = apiCall("Admin/Sku/queryNoPaging", array($map));
		if(!$result['status']){
			$this->error($result['info']);
		}
		$sku_result = $result['info'];
		
		$map = array();
		$map['id'] = array('in',$sku_value_ids);
		
		$result = apiCall("Admin/Skuvalue/queryNoPaging", array($map));
		if(!$result['status']){
			$this->error($result['info']);
		}
		$sku_value_result = $result['info'];
		//上述代码获取SKU以及SKU值的名称
				
		$sku_arr = array();
		foreach($sku_result as $_sku){
			$key = $_sku['id'].':';
			foreach($sku_value_result as $_sku_value){
					if($_sku_value['sku_id'] == $_sku['id']){
//						$tmpKey = $key . $_sku_value['id'];
//						$sku_arr[$tmpKey] = array('sku_id'=>$_sku['id'],'sku_name'=>$_sku['name'],'sku_value_name'=>$_sku_value['name']);
						if(!isset($sku_arr[$_sku['id']])){
							$sku_arr[$_sku['id']] = array('id'=>$_sku['id'],'sku_name'=>$_sku['name'],'sku_value_list'=> array());
						}
						
						array_push($sku_arr[$_sku['id']]['sku_value_list'],array('id'=>$_sku_value['id'],'name'=>$_sku_value['name']));
						
					}
			}
		}
		
		$result = apiCall("Admin/WxproductSku/queryNoPaging", array(array('product_id'=>$product['id'])));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$formatSku = array();
		foreach($result['info'] as &$vo){
			$formatSku[$vo['sku_id']] = array(
				'icon_url'=>$vo['icon_url'],
				'ori_price'=> $vo['ori_price'],
				'price'=>$vo['price'],
				'product_code'=>$vo['product_code'],
				'product_id'=>$vo['product_id'],
				'quantity'=>$vo['quantity'],
			);
		}
		return array('sku_list'=>$formatSku,'sku_arr'=>$sku_arr);
	}
	
	
	/**
	 * 获取属性对应的文字描述
	 */
	private function getProperties($prop){
		if(empty($prop)) { return array(); }
		$prop_arr =explode(";", $prop);
		$prop_ids = array();
		$propvalue_ids = array();
		$result = array();
		foreach($prop_arr as $vo){
			if($vo){
				$prop_value = explode(",", $vo);
				array_push($prop_ids,$prop_value[0]);
				array_push($propvalue_ids,$prop_value[1]);
			}
		}
		
		$map = array();
		$map['id'] = array("in",$prop_ids);
		$prop_result = apiCall("Admin/CategoryProp/queryNoPaging", array($map));
		if(!$prop_result){
			$this->error($prop_result['info']);
		}
		
		$prop_result = $prop_result['info'];
		
		
		$map = array();
		$map['id'] = array("in",$propvalue_ids);
		$propvalue_result = apiCall("Admin/CategoryPropvalue/queryNoPaging", array($map));
		if(!$propvalue_result){
			$this->error($propvalue_result['info']);
		}
		
		$propvalue_result = $propvalue_result['info'];
		
		for($i=0;$i<count($prop_result);$i++){
			$p = $prop_result[$i];
			$pv = $propvalue_result[$i];
			
			array_push($result,array('name'=>$p['propname'],'value'=>$pv['valuename']));
			
		}
		
		
		
		return $result;
	}

	
	/**
	 * 从商品信息中提取图片
	 */
	private function getBanners($product){
		
		$imgs = explode(",", $product['img']);
		array_pop($imgs);		
		array_push($imgs,$product['main_img']);
		return $imgs;
	}
	
}
