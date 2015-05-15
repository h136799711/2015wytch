<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Controller;

class WxshopController extends AdminController{
	
	protected function _initialize(){
		
		//TODO: 导致session，修改不启作用，沿用上次，导致一级菜单未能存入session，使得当前激活菜单不正确
		//FIXME:考虑，将图片上传放到另外一个类中
		//解决uploadify上传session问题
		session('[pause]');
		$session_id = I('get.session_id','');
		if (!empty($session_id)) {
		    session_id($session_id);
			session('[start]');
		}
		
		parent::_initialize();
		
	}
	
/**
	 * 商城配置
	 */
	public function config(){
		if(IS_GET){
			$map = array('name'=>"WXPAY_OPENID");
			$result = apiCall("Admin/Config/getInfo", array($map));
			if($result['status']){
				$this->assign("wxpayopenid",	$result['info']['value']);
				$this->display();
			}
		}elseif(IS_POST){
			
			$openids = I('post.openids','');
			
			$config = array("WXPAY_OPENID"=>$openids);
			$result = apiCall("Admin/Config/set", array($config));
			if($result['status']){
				C('WXPAY_OPENID',$openids);
				
				$this->success(L('RESULT_SUCCESS'),U('Shop/config'));
			}else{
				if(is_null($result['info'])){
					$this->error("无更新！");
				}else{
					$this->error($result['info']);
				}
			}
			
		}
	}
	
	/**
	 * 店铺管理
	 */
	public function index(){

		
		//get.startdatetime
		//分页时带参数get参数
		$name = I('name','');

		$map = array();
		if(!empty($name)){
			$map['name'] = array('like',"%".$name."%");
		}
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";
		$map['uid'] = UID;
		//
		$result = apiCall('Admin/Wxstore/query', array($map, $page, $order));
		//
		if ($result['status']) {
			$this -> assign('name', $name);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error(L('UNKNOWN_ERR'));
		}
	}
	
	
	public function add(){
		if(IS_GET){
			$this->display();
		}elseif(IS_POST){
			$name = I('post.name','店铺名称');//
			$desc = I('post.desc','');
			$type = I('post.type','');
			$logo = I('post.logo','');
			$banner = I('post.banner','');
			$wxno = I('post.wxno','');
			$cate_id = I('post.store_type','');
			$wxnum = I('post.weixin_number','');
			$weixin_name = I('post.weixin_number_name','');
			$weixin = array();
			$wxnum = explode(",",$wxnum);
			$weixin_name = explode(",",$weixin_name);
			$lat = I('post.lat',30.314933);
			$lng = I('post.lng',120.337985);
			
//			dump($wxnum);
//			dump($weixin_name);
			for($i=0;$i<count($wxnum);$i++){
				if(!empty($weixin_name[$i])){
					array_push($weixin,array('openid'=>$wxnum[$i],'name'=>$weixin_name[$i]));
				}
			}
			$service_phone = I('post.service_phone','');
			
			$entity = array(
				'latitude'=>$lat,
				'longitude'=>$lng,
				'wxno'=>$wxno,
				'uid'=>UID,
				'name'=>$name,
				'desc'=>$desc,
				'logo'=>$logo,
				'banner'=>$banner,
				'isopen'=>0,
				'cate_id'=>$cate_id,
				'notes'=>I('post.notes',''),
				'weixin_number'=>json_encode($weixin),
				'service_phone'=>$service_phone,
			);
//			dump($entity);
//			exit();
			$result = apiCall("Admin/Wxstore/add",array($entity));
//			dump($result);
			if($result['status']){
				$this->success("操作成功！",U('Admin/Wxshop/index'));
			}else{
				$this->error($result['info']);
			}
		}
	}
	
	
	
	public function edit(){
		if(IS_GET){
			$id = I('get.id',0);
			$map = array('id'=>$id);
			$result = apiCall("Admin/Wxstore/getInfo",array($map));
			if($result['status']){
				$weixin = json_decode($result['info']['weixin_number']);
				$text = "";

				foreach($weixin as $vo){
					$text = $text.$vo->openid.",";
				}
				
				$this->assign("weixin",$weixin);
				$this->assign("openids",$text);
				
				$this->assign("store",$result['info']);
				$this->display();
			}else{
				$this->error($result['info']);
			}
		}elseif(IS_POST){
			$id = I('post.id',0);
			$lat = I('post.lat',30.314933);
			$lng = I('post.lng',120.337985);
			$name = I('post.name','店铺名称');//
			$desc = I('post.desc','');
			$wxno = I('post.wxno','');
			$type = I('post.type','');
			$logo = I('post.logo','');
			$banner = I('post.banner','');
			$cate_id = I('post.store_type','');
			$wxnum = I('post.weixin_number','');
			$weixin_name = I('post.weixin_number_name','');
			$weixin = array();
			$wxnum = explode(",",$wxnum);
			$weixin_name = explode(",",$weixin_name);
			
			for($i=0;$i<count($wxnum);$i++){
				if(!empty($weixin_name[$i])){
					array_push($weixin,array('openid'=>$wxnum[$i],'name'=>$weixin_name[$i]));
				}
			}
			
			$service_phone = I('post.service_phone','');
			
			$entity = array(
				'wxno'=>$wxno,
				'name'=>$name,
				'desc'=>$desc,
				'logo'=>$logo,
				'latitude'=>$lat,
				'longitude'=>$lng,
				'banner'=>$banner,
				'cate_id'=>$cate_id,
				'notes'=>I('post.notes',''),
				'weixin_number'=>json_encode($weixin),
				'service_phone'=>$service_phone,
			);
			
			$result = apiCall("Admin/Wxstore/saveByID",array($id,$entity));

			if($result['status']){
				$this->success("操作成功！",U('Admin/Wxshop/index'));
			}else{
				$this->error($result['info']);
			}
		}
	}
	
	public function open(){
		$isopen = 1-I('get.isopen',0);
		
		$id = I('id', -1);
				
		$entity = array(
			'isopen'=>$isopen
		);
		$result = apiCall('Admin/Wxstore/saveByID', array($id,$entity));

		if ($result['status'] === false) {
			LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		} else {
			$this -> success(L('RESULT_SUCCESS'), U('Admin/' . CONTROLLER_NAME . '/index'));
		}
		
		
	}
	
	public function delete(){
		$map = array('id' => I('id', -1));
		
		$result = apiCall("Admin/Wxproduct/queryNoPaging",array(array('storeid'=>$map['id'])));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		if(is_array($result['info']) && count($result['info']) > 0){
			$this->error("该商店尚有相关联数据，无法删除！");			
		}
		
		
		
		$result = apiCall('Admin/Wxstore/delete', array($map));

		if ($result['status'] === false) {
			LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		} else {
			$this -> success(L('RESULT_SUCCESS'), U('Admin/' . CONTROLLER_NAME . '/index'));
		}
	}
	
	//==============================其它功能接口
	
	public function picturelist(){
		if(IS_AJAX){
			$cur = I('post.p',0);
			$size = I('post.size',10);
			$map = array('uid'=>UID);
			$page = array('curpage'=>$cur,'size'=>$size);
			$order = 'createtime desc';
			$params = array(
				'p'=>$cur,
				'size'=>$size,
			);
			$fields = 'id,createtime,status,path,url,md5,imgurl,ori_name,savename,size';
//			query($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false)
	        $result = apiCall('Admin/WxshopPicture/query',array($map,$page,$order,$params,$fields));
			if($result['status']){
				$this->success($result['info']);
			}else{
				$this->error($result['info']);
			}
		}
	}
	
	
	/**
	 * 上传图片接口
	 */
	public function uploadPicture(){
		if(IS_POST){
			
			if(!isset($_FILES['wxshop'])){
				$this->error("文件对象必须为wxshop");
			}
			
//			$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
			$tmp_name = $_FILES['wxshop']['tmp_name'];
			
			//1.上传到微信
//			$result = $wxshopapi->uploadImg(time().".jpg",$tmp_name);
//			
//			if(!$result['status']){
//				$this->error($result['info']);
//			}

			$result['info'] = "";
			//2.再上传到自己的服务器，
			//TODO:也可以上传到QINIU上
	        /* 返回标准数据 */
	        $return  = array('status' => 1, 'info' => '上传成功', 'data' => '');
			
	        /* 调用文件上传组件上传文件 */
	        $Picture = D('WxshopPicture');
			$extInfo = array('uid' => UID,'imgurl' => $result['info']);
	        $info = $Picture->upload(
	            $_FILES,
	            C('WXSHOP_PICTURE_UPLOAD')
	            ,$extInfo
			); 
			
	        /* 记录图片信息 */
	        if($info){
	            $return['status'] = 1;
	            $return = array_merge($info['wxshop'], $return);
	        } else {
	            $return['status'] = 0;
	            $return['info']   = $Picture->getError();
	        }
	
	        /* 返回JSON数据 */
	        $this->ajaxReturn($return);
		}
		
	}
	
	
	
	/**
	 * ajax获取类目信息
	 */
	public function cate(){
		$cate_id = I('cateid',-1);
		if($cate_id == -1){
			$this->success(array());
		}
		
//		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
//		$result = $wxshopapi->category($cate_id);		
		
		$map = array('parent'=>$cate_id);
		
		$result = apiCall("Admin/Category/queryNoPaging", array($map));
		
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
	}
	
}
