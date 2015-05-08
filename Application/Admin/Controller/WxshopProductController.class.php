<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class WxshopProductController extends AdminController {
	
	public function group(){
		$id = I('id',0);
		if(IS_GET){
			$storeid = I('get.storeid',0);
			$map = array('parentid'=>C('DATATREE.WXPRODUCTGROUP'));
			$result = apiCall("Admin/Datatree/queryNoPaging",array($map));
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			
			$this->assign("groups",$result['info']);
			$this->assign("storeid",$storeid);
			
			$result = apiCall("Admin/WxproductGroup/queryNoPaging",array(array('p_id'=>$id)));
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->assign("addedgroups",$this->getGroups($result['info']));
			$this->assign("id",$id);
			$this->display();
		}else{
			
		}
	}
	
	/**
	 * 
	 */
	private function getGroups($groups){
		$str = "";
		foreach($groups as $vo){
			$str = $str.$vo['g_id'].',';
		}
		
		return $str;
		
	}
	
	/**
	 * 商品运费设置
	 */
	public function express(){
		if(IS_GET){
			
			$productid = I('get.productid','');
			$id = I('get.id',0);
			
			$result = apiCall("Admin/Wxproduct/getInfo", array(array('product_id'=>$productid) ));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			if(is_null($result['info'])){
				$this->error("警告：商品信息获取失败！");
			}
			
			$location = $result['info']['loc_country'].">>".$result['info']['loc_province'].">>".$result['info']['loc_city'].">>".$result['info']['loc_address'];
			
			$this->assign("storeid",$result['info']['storeid']);
			$this->assign("location",$location);
			$this->assign("delivery_type",$result['info']['delivery_type']);
			$tmp = json_decode($result['info']['express'],JSON_UNESCAPED_UNICODE);
			$this->assign("express",$tmp);
			if(count($tmp) > 0){
				foreach($tmp as $vo){
					$express[$vo['id']] = $vo['price']/100.0;
				}
				$this->assign("express",$express);
			}
			$this->assign("template_id",$result['info']['template_id']);
			
			$result = apiCall("Tool/Province/queryNoPaging", array(array('countryid'=>1017) ));
			if($result['status']){				
				$this->assign("province",$result['info']);
			}else{
				$this->error("警告：省份信息获取失败！");
			}
//			$wxshopapi = new \Common\Api\WxShopApi($this -> appid, $this -> appsecret);
			
//			$result = $wxshopapi->expressGetAll();
////			dump($result);
//			if($result['status']){	
//				$this->assign("expresslist",$result['info']);				
//			}else{
//				$this->error("警告：运费信息获取失败！");
//			}
			
			$this->assign("countrylist",C('COUNTRY_LIST'));
			$this->assign("productid",$productid);
			$this->assign("id",$id);
			$this->display();
		}else{
//			delivery_info		
//					delivery_type	
//					template_id	
//					express	= []
//						id
//						price	
			$query = I('post.query','','htmlspecialchars_decode');
			$query = json_decode($query,JSON_UNESCAPED_UNICODE);
			
//			dump($query);
			
			$productid = I('post.productid','');
			if(empty($productid)){
				$this->error("商品ID失效！");
			}
			
			$flag =  $query['islocchange'];
			$entity = array();
			if($flag){
				$entity['loc_country'] = $query['country'];
				$entity['loc_province'] = $query['province'];
				$entity['loc_city'] = $query['city'];
				$entity['loc_address'] = $query['area'];
			}
			
			$flag = $query['haspostfee'];
			$templateid = intval($query['templateid']);
			if($flag){
				$entity['attrext_ispostfree'] = 0;
				if($templateid > 0){
					$entity['delivery_type'] = 1;
					$entity['template_id'] = $templateid;
					$entity['express'] = '';
				}else{
					$entity['delivery_type'] = 0;
					foreach($query['express'] as &$vo){
						$vo['price'] = $vo['price']*100.0;
						$vo['id'] = intval($vo['id']); 
					}
					$entity['express'] = json_encode($query['express'],JSON_UNESCAPED_UNICODE);
					$entity['template_id'] = 0;
				}
			}else{
				$entity['attrext_ispostfree'] = 1;
				$entity['delivery_type'] = -1;
			}
			
//			dump($entity);
			
			$result = apiCall("Admin/Wxproduct/save", array(array('product_id'=>$productid),$entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("操作成功！");
			
		}
	}
	
	
	/**
	 * 商品SKU 管理
	 */
	public function sku(){
		
		if(IS_GET){
			$id = I('get.id',0);
			
			$result = apiCall("Admin/Wxproduct/getInfo", array(array('id'=>$id) ));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			if(is_null($result['info'])){
				$this->error("警告：商品信息获取失败！");
			}
			
			if($result['info']['has_sku'] == 1){
				//多规格
				$skuinfo = $result['info']['sku_info'];
				
				$this->assign("skuinfo",$this->getSkuValue(json_decode($skuinfo,JSON_UNESCAPED_UNICODE)));
				
				$skulist = apiCall("Admin/WxproductSku/queryNoPaging", array(array('product_id'=>$id)));
				if($skulist['status']){
					$this->assign("skuvaluelist",json_encode($skulist['info'],JSON_UNESCAPED_UNICODE));
				}
			}
			
			$this->assign("has_sku",$result['info']['has_sku']);
			$this->assign("storeid",$result['info']['storeid']);

			
			$cate_id = $result['info']['cate_id'];

			$result = apiCall("Admin/Sku/querySkuTable",array($cate_id));
			
			if($result['status']){
				$this->assign("skulist",$this->color2First($result['info']));
			}
			
			//SKU
			$result = apiCall("Admin/Category/getInfo",array(array('id'=>$cate_id)));
			if(!$result['status']){
				$this->error($result['info']);
			}
			$level = 0;
			$parent = 0;
			$preparent = -1;
			
			if(is_array($result['info'])){
				$level = $result['info']['level'];
				$parent = $result['info']['parent'];
				$result = apiCall("Admin/Category/getInfo",array(array('id'=>$parent)));
				if(!$result['status']){
					$this->error($result['info']);
				}
				$preparent = $result['info']['parent'];		
			}
			
			$this->assign("cate_id",$level);
			$this->assign("parent",$parent);
			$this->assign("preparent",$preparent);
			$this->assign("cate_id",$cate_id);
			$this->assign("id",$id);
			$this->display();
		}else{
			
			$id = I('post.id',0);
			$has_sku = I('post.has_sku',1);
			
			if($has_sku == 0){
				$entity = array(
					'sku_info'=>'',
					'has_sku'=>0,
				);
				
				$result = apiCall("Admin/Wxproduct/saveByID", array($id,$entity));
				
				if(!$result['status']){
					$this->error($result['info']);
				}
				
				$this->success("保存成功！");
				
			}
			
			$sku_list = I('post.sku_list','');
			$sku_info = I('post.sku_info','');
			
			$sku_info = json_decode(htmlspecialchars_decode($sku_info),JSON_UNESCAPED_UNICODE);
			$sku_list = json_decode(htmlspecialchars_decode($sku_list),JSON_UNESCAPED_UNICODE);	
			
			$result = apiCall("Admin/WxproductSku/addSkuList", array($id,$sku_info,$sku_list));
			
			if(!$result['status']){				
				$this->error($result['info']);
				
			}else{
				
				$this->success("保存成功！");

			}
		}
	}

	
	
	/**
	 * 
	 */
	private function getSkuValue($skuvalue){
		$valuelist = "";
		foreach($skuvalue as $value){
			foreach($value['vid'] as $vo){
				$valuelist = $valuelist.$vo.",";
			}
		}
		return $valuelist;
	}
	
	/**
	 * 商品详情页/新增
	 * @param $get.productid  商品ID
	 * @param $get.storeid 店铺ID
	 */
	public function detail(){
		if(IS_GET){
			$productid = I('get.productid','');
			$id = I('get.id',0);
			$storeid = I('get.storeid',0);
			if(empty($productid)){
				$this->error("缺少商品ID");
			}
			if(empty($storeid)){
				$this->error("缺少店铺ID");
			}
			$map['product_id'] = $productid;
			$result = apiCall("Admin/Wxproduct/getInfo",array($map));
			if($result['status']){
				$detail = $result['info']['detail'];
				
			}else{
				$this->error("商品信息获取失败！");
			}
			
			
			$this->assign("detail",json_decode(htmlspecialchars_decode($detail),JSON_UNESCAPED_UNICODE));
			$this->assign("productid",$productid);
			$this->assign("storeid",$storeid);
			$this->assign("id",$id);
			$this->display();
			
		}else{
			$detail = I("post.detail",'');
			$productid = I("post.productid",'');
				
			$product_detail = array();
			
			if(!empty($detail)){
				$detail_decode = json_decode(htmlspecialchars_decode($detail),JSON_UNESCAPED_UNICODE);				
			}else{
				$this->error("详情为空");
			}
			
			$map['product_id'] = $productid;
			$result = apiCall("Admin/Wxproduct/save",array($map,array('detail'=>$detail)));
			if($result['status']){
				$this->success("修改成功！");
			}else{
				$this->error($result['info']);
			}
		}
	}
	
	/**
	 * 首页/商品管理页面
	 */
	public function index() {
		$onshelf = I('onshelf', 0);
		$storeid = I('storeid', 0, "intval");
		if (empty($storeid)) {
			$this -> error("缺少店铺ID参数！");
		}
		//get.startdatetime
		//分页时带参数get参数
		$params = array('onshelf' => $onself);
		$name = I('post.name', '');

		$map = array();
		if (!empty($name)) {
			$map['name'] = array('like', '%'.$name.'%');
			$params['name'] = $name;
		}
		$map['onshelf'] = $onshelf;
		$map['storeid'] = $storeid;
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";
		//
		$result = apiCall('Admin/Wxproduct/query', array($map, $page, $order, $params));
		//
		if ($result['status']) {
			$this -> assign('name', $name);
			$this -> assign('onshelf', $onshelf);
			$this -> assign('storeid', $storeid);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			
			
			$store = apiCall('Admin/Wxstore/getInfo', array(array('id'=>$storeid)));
			if(!$store['status']){
				$this->error($store['info']);
			}
			$this->assign("store",$store['info']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error(L('UNKNOWN_ERR'));
		}
	}


	/**
	 * 商品上下架
	 * @param $success_url 删除成功后跳转
	 */
	public function shelf() {
		$status = I('get.on',0,'intval');
		$map = array('id' => I('get.id', -1));
		
		$entity['onshelf'] = $status;
		$result = apiCall('Admin/Wxproduct/save', array($map,$entity));
		
		if ($result['status'] === false) {
			LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		} else {
			$this -> success(L('RESULT_SUCCESS'));
		}

	}

	/**
	 * 单个删除
	 * @param $success_url 删除成功后跳转
	 */
	public function delete($success_url = false) {
		
		if ($success_url === false) {
			$success_url = U('Admin/WxshopProduct/index',array('storeid'=>$storeid));
		}
		
		//TODO: 检测商品的其它数据是否存在
		$map = array('id' => I('id', -1));
		
		$result = apiCall('Admin/Wxproduct/delete', array($map));
		
		if ($result['status'] === false) {
			LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		} else {
			$this -> success(L('RESULT_SUCCESS'));
		}

	}
	
	

	/**
	 * 商品预创建－选择类目
	 */
	public function precreate() {
		
		if (IS_POST) {
			//保存
		} else {
			
			$map = array('parent'=>0);
			$result = apiCall("Admin/Category/queryNoPaging", array($map));
			
			$storeid = I('get.storeid', 0);
			
			if ($storeid == 0) {
				$this -> error("缺少商铺ID参数");
			}

			$this -> assign("storeid", $storeid);
			if ($result['status']) {
				$this -> assign("rootcate", $result['info']);
			}
			$this -> display();
		}
	}

	/**
	 * 添加商品
	 *
	 */
	public function create() {

		if (IS_POST) {
						
			$base_attr = $this -> getBaseAttr();
			$storeid = I('storeid', 0);

			if ($storeid == 0) {
				$this -> error("缺少商铺ID参数");
			}

			$attrext = array('isPostFree' => 1, 'isHasReceipt' => I('post.ishasreceipt', 0), 'isUnderGuaranty' => I('post.isunderguaranty', 0), 'isSupportReplace' => I('post.issupportreplace', 0), 'location' => array('country' => '中国', 'province' => '四川省', 'city' => '内江市', 'address' => '威远县'));
			$sku_list = $this -> getSKUList();
			
			$product = array('product_base' => $base_attr, 'attrext' => $attrext, 'sku_list' => $sku_list);

			$product_id = GUID();
			

			$result = $this -> addToProduct($storeid, $product_id, $product);
			
			if ($result['status']) {
				$this -> success("操作成功!", U('Admin/WxshopProduct/index', array('storeid' => $storeid)));
			} else {
				$this -> error($result['info']);
			}

		} else {
			$catename = I('catename', '');
			$storeid = I('storeid', 0);			
			$cates = I("get.cates", '');
			$cates = explode("_", $cates);
			if (count($cates) <= 1) {
				$this -> error("商品类目错误！");
			}
			
			$this -> assign("cate_id", $cates[count($cates)-1]);
			$this -> assign("storeid", $storeid);
			$this -> assign("catename", $catename);
			$this -> assign("cates", I('cates', ''));
			$this -> display();
		}
	}
	
	/**
	 * 商品信息编辑
	 */
	public function edit(){
		if(IS_GET){
			
			$id = I('get.id',0);
			$result = apiCall("Admin/Wxproduct/getInfo", array(array('id'=>$id)));
			
			if($result['status']){
				$imgs = explode(",",$result['info']['img']);
				
				array_pop($imgs);
				$this->assign("imgs",$imgs);
				$this->assign("vo",$result['info']);
			}
			
			$this->display();
		}else{
			$id = I('post.id',0);
			$buylimit = I('buylimit',0);
			if(empty($buylimit)){
				$buylimit = 0;
			}
			$price = I('price',0,'intval');
			$price = $price * 100.0;
			$ori_price = I('ori_price',0,'intval');
			$ori_price = $ori_price * 100.0;
			$entity = array(
				'main_img'=>I('main_img',''),
				'img'=> I('post.img', ''),
				'name'=>I('product_name',''),
				'price'=>$price,
				'ori_price'=>$ori_price,
				'quantity'=>I('quantity',0),
				'buy_limit'=>$buylimit,
				'attrext_ishasreceipt'=>I('ishasreceipt',0),
				'attrext_isunderguaranty'=>I('isunderguaranty',0),
				'attrext_issupportreplace'=>I('issupportreplace',0),
			);
			$result = apiCall("Admin/Wxproduct/saveByID",array($id,$entity));
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success(L('RESULT_SUCCESS'));
			
		}
	}

	/**
	 * 指定分类的所有属性
	 */
	public function cateAllProp() {

		if (IS_AJAX) {
			$cate_id = I('cate_id', 0);
			$map = array('cate_id'=>$cate_id);
			$result = apiCall("Admin/CategoryProp/queryPropTable", array($map));
			
			if ($result['status']) {
				$this -> success($result['info']);
			} else {
				$this -> error($result['info']);
			}
		}
	}
		
	//==========================私有方法
	/**
	 * 将产品信息保存到数据库
	 */
	private function addToProduct($storeid, $productid, $product) {

		$has_sku = I('post.has_sku', 0, 'intval');

		$entity = array(
					'storeid' => $storeid, 
					'wxaccountid' => getWxAccountID(), 
					'product_id' => $productid, 
					'name' => $product['product_base']['name'], 
					'main_img' => $product['product_base']['main_img'], 
					'img' => I('post.img', ''), 
					'buy_limit' => $product['product_base']['buy_limit'], 
					'cate_id' => $product['product_base']['category_id'][0], 
					'delivery_type' => -1, // 包邮
					'template_id' => '', 
					'express_id' => 0, 
					'express_price' => 0, 
					'attrext_ispostfree' => $product['attrext']['isPostFree'], 
					'attrext_ishasreceipt' => $product['attrext']['isHasReceipt'], 
					'attrext_isunderguaranty' => $product['attrext']['isUnderGuaranty'], 
					'attrext_issupportreplace' => $product['attrext']['isSupportReplace'], 
					'loc_country' => $product['attrext']['location']['country'], 
					'loc_province' => $product['attrext']['location']['province'], 
					'loc_city' => $product['attrext']['location']['city'], 
					'loc_address' => $product['attrext']['location']['address'], 
					'has_sku' => intval($has_sku), 
					'detail' => '', 
					'onself' => '0', 
					'status' => 1,
					'properties'=> I('post.property', ''),
					'sku_info'=>'',
				);
		if ($has_sku == 0) {
			$entity['ori_price'] = $product['sku_list'][0]['ori_price'];
			$entity['price'] = $product['sku_list'][0]['price'];
			$entity['quantity'] = $product['sku_list'][0]['quantity'];
			$entity['product_code'] = $product['sku_list'][0]['product_code'];
		}
		
		$result = apiCall("Admin/Wxproduct/add", array($entity));

		return $result;
	}

	/**
	 * 产品与分组进行关联
	 */
	private function productToGroup($wxshopapi, $productid) {
		
		$groups = I('groups', '');
		if ($groups) {
//			foreach ($groups as $vo) {
//				$product_list = array('product_id' => $productid, 'mod_action' => 1);
//				$result = $wxshopapi -> groupModProduct($vo, array($product_list));
//			}
		}

	}
	
	private function getBaseAttr() {

		$cates = I("post.cates", '');
		$cates = explode("_", $cates);
		if (count($cates) <= 1) {
			$this -> error("商品类目错误！");
		}

		//属性
		$property = I('post.property', '');
		$property = explode(";", $property);
		$properties = array();
		//		dump($property);
		foreach ($property as $vo) {
			$prop = explode(",", $vo);
			if (count($prop) == 2) {
				$properties[] = array('id' => $prop[0], 'vid' => $prop[1]);
			}
		}

		//SKU
		$sku_info = array();

		$category = $cates[count($cates) - 1];
		$main_img = I('post.main_img', '');
		if (I('post.isbuylimit', '0') == 1) {
			$buylimit = I('post.buylimit', 0);
		} else {
			$buylimit = 0;
		}
		$imglist = array();
		$img = explode(",", I('post.img', ''));
		//		dump($img);
		foreach ($img as $vo) {
			if ($vo) {
				$imglist[] = $vo;
			}
		}

		//
		return array('name' => I('post.product_name', ''), 
		'category_id' => array($category), 
		'img' => $imglist,
		 'main_img' => $main_img, 
		'datail' => array(), 
		'property' => $properties, 
		'sku_info' => $sku_info, 
		'buy_limit' => $buylimit);
	}

	private function getSKUList() {
		
		$has_sku = I('post.has_sku', '0');

		if ($has_sku == "0") {
			$ori_price = I('post.ori_price', 0, 'intval');
			$price = I('post.price', 0, 'intval');
			//统一规格
			$sku = array('sku_id' => '', //商品添加时默认为统一规格
			'icon_url' => I('post.main_img', ''),
			 'ori_price' => $ori_price * 100, 'price' => $price * 100, 
			 'quantity' => I('post.quantity', 0, 'intval'), 
			 'product_code' => I('post.product_code', "")
			  );
		} else {
			//商品添加页面不增加多规格功能
			//post.ori_price[]

		}
		return array($sku);
	}
	
	
	/**
	 * 将本地数据库存储的商品信息转换为微信商品信息
	 * @param $localproduct wxproduct表的一条信息
	 */
	private function toWeixinproduct($localproduct){
		$imglist = array();
		$img = explode(",", $localproduct['img']);
		//		dump($img);
		foreach ($img as $vo) {
			if ($vo) {
				$imglist[] = $vo;
			}
		}
		//处理SKU_INFO
		
		$sku_info = json_decode($localproduct['sku_info']);
		
		
		//处理商品属性
		$property = $localproduct['properties'];
		$property = explode(";", $property);
		$properties = array();
		foreach ($property as $vo) {
			$prop = explode(",", $vo);
			if (count($prop) == 2) {
				$properties[] = array('id' => $prop[0], 'vid' => $prop[1]);
			}
		}
		
		//商品基本信息
		$base_attr = array(
			'name' => $localproduct['name'],
		 	'category_id' => array($localproduct['cate_id']), 
		 	'img' => $imglist, 
		 	'main_img' => $localproduct['main_img'], 
			'property' => $properties, 
			'sku_info' => $sku_info, 
			'buy_limit' => intval($localproduct['buy_limit']), 
			'detail'=>array(),
		);
		
		//商品详情
		if(!empty($localproduct['detail'])){
			$detail_arr = json_decode(htmlspecialchars_decode($localproduct['detail']),JSON_UNESCAPED_UNICODE);
			for($i=0;$i<count($detail_arr);$i++){
				if($detail_arr[$i]['type'] == 'text'){
					array_push($base_attr['detail'],array('text'=>$detail_arr[$i]['ct']));
				}else{
					array_push($base_attr['detail'],array('img'=>$detail_arr[$i]['ct']));
				}
			}
		}
		
		
		//属性
		$attrext = array(
			'isPostFree' => intval($localproduct['attrext_ispostfree']) , 
			'isHasReceipt' =>intval($localproduct['attrext_ishasreceipt']) , 
			'isUnderGuaranty' => intval($localproduct['attrext_isunderguaranty']) , 
			'isSupportReplace' => intval($localproduct['attrext_issupportreplace']) , 
			'location' => array(
				'country' => $localproduct['loc_country'], 
				'province' => $localproduct['loc_province'], 
				'city' => $localproduct['loc_city'], 
				'address' => $localproduct['loc_address'], 
			)			
		);
		
		$sku_list = array();
		
		if($localproduct['has_sku'] == 0){
			array_push($sku_list,array(			
			'sku_id'=>''	,	
			'ori_price'=>floatval($localproduct['ori_price']),
			'price' => floatval($localproduct['price']),
			'quantity' => intval($localproduct['quantity']),
			'product_code' => $localproduct['product_code'],
			'icon_url'=>$localproduct['main_img']
			));
		}else{
			//多规格情况下处理
			//去wxproduct_sku 查询
			$skulist = apiCall("Admin/WxproductSku/queryNoPaging", array(array('product_id'=>$localproduct['product_id'])));
			$sku_list = $skulist['info'];
			foreach($sku_list as &$vo){
				unset($vo['id']);
				unset($vo['product_id']);
				unset($vo['createtime']);
			}
		}
		
		$delivery_info = [];
		
		if($localproduct['attr_ispostfree'] == 0){
			if($localproduct['delivery_type'] == 1){
				$delivery_info['delivery_type'] = 1;
				$delivery_info['template_id'] = intval($localproduct['template_id']);
				$delivery_info['express'] = [];
			}else{
				$delivery_info['delivery_type'] = 0;
				$delivery_info['template_id'] = 0;
				$delivery_info['express'] = json_decode($localproduct['express']);
			}
		}
		
		$return = array(
			'product_base' => $base_attr, 
			'attrext' => $attrext, 
			'sku_list' => $sku_list
		);
		
		if(count($delivery_info) > 0){
			$return['delivery_info'] = $delivery_info;
		}
		
		return $return;
	}
	
	/**
	 * 将颜色SKU 放在最前面
	 */
	private function color2First($skulist){
		$colorIndex = 0;
		for($i=0;$i<count($skulist);$i++){
			if($skulist[$i]->name == "颜色"){
				$colorIndex = $i;
				break;
			}
		}
		
		if($colorIndex > 0){
			$temp = $skulist[0];
			$skulist[0] = $skulist[$colorIndex];
			$skulist[$colorIndex] = $temp;
		}
		return $skulist;
		
	}
}
