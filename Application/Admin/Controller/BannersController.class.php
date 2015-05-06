<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class BannersController extends  AdminController{
	
	public function index(){
		
		
		
		$map = array();
		$map = array('uid'=>UID);
		$map['position'] = array("in","18,20");
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";
		//
		$result = apiCall('Admin/Banners/queryWithPosition', array($map, $page, $order, $params));
		//
		if ($result['status']) {
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
		}else{
			$title = I('post.title','');
//			$url = 
			$notes = I('post.notes','');
			$position = I('post.position',18);
			if(empty($position)){
				$this->error("配置错误！");
			}
			$entity = array(
				'uid'=>UID,
				'position'=>$position,
				'storeid'=>-1,
				'title'=>$title,
				'notes'=>$notes,
				'img'=>I('img',''),
				'url'=>I('url',''),
				'starttime'=>0,
				'endtime'=>0,
				'noticetime'=>0,
			);
		
			
			$result = apiCall("Admin/Banners/add", array($entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("保存成功！",U('Admin/Banners/index'));
			
		}
	}
	
	
	public function edit(){
		$id = I('id',0);
		if(IS_GET){
			$result = apiCall("Admin/Banners/getInfo", array(array('id'=>$id)));
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->assign("vo",$result['info']);
			$this->display();
		}else{
			$title = I('post.title','');
//			$url = 
			$notes = I('post.notes','');
			$position = I('post.position',18);
			if(empty($position)){
				$this->error("配置错误！");
			}
			$entity = array(
				'position'=>$position,
				'title'=>$title,
				'notes'=>$notes,
				'img'=>I('post.img',''),
				'url'=>I('post.url',''),
			);
		
			
			$result = apiCall("Admin/Banners/saveByID", array($id,$entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("保存成功！",U('Admin/Banners/index'));
			
		}
	}
		
	
	
	
}
