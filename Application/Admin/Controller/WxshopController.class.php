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
		parent::_initialize();
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
			$map['name'] = array('like',"%"+$name+"%");
		}
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";
		
		//
		$result = apiCall('Admin/Wxstore/query', array($map, $page, $order, $params));
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
			$entity = array(
				'uid'=>UID,
				'name'=>$name,
				'desc'=>$desc
			);
			
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
				$this->assign("store",$result['info']);
				$this->display();
			}else{
				$this->error($result['info']);
			}
		}elseif(IS_POST){
			$id = I('post.id',0);
			$name = I('post.name','店铺名称');//
			$desc = I('post.desc','');
			$entity = array(
				'name'=>$name,
				'desc'=>$desc
			);
			
			$result = apiCall("Admin/Wxstore/saveByID",array($id,$entity));

			if($result['status']){
				$this->success("操作成功！",U('Admin/Wxshop/index'));
			}else{
				$this->error($result['info']);
			}
		}
	}

	public function delete(){
		$map = array('id' => I('id', -1));

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
			
			$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
			$tmp_name = $_FILES['wxshop']['tmp_name'];
			//1.上传到微信
			$result = $wxshopapi->uploadImg(time().".jpg",$tmp_name);
			
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
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
		$wxshopapi = new \Common\Api\WxShopApi($this->appid,$this->appsecret);
		
			
		$result = $wxshopapi->category($cate_id);
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
	}
	
}
