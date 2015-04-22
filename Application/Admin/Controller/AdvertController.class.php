<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class AdvertController extends  AdminController{
	
	protected $position;
	
	protected function _initialize(){
		parent::_initialize();
		$this->position = C('DATATREE.SHOP_INDEX_ADVERT');
	}

	public function index(){
		
		$map = array();
		$map = array('uid'=>UID,'position'=>$this->position);
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " createtime desc ";
		//
		$result = apiCall('Admin/Banners/query', array($map, $page, $order, $params));
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
			$position = $this->position;
			$starttime = I('post.startdatetime',FALSE);
			$endtime = I('post.enddatetime',FALSE);
			$noticetime = I('post.noticedatetime',FALSE);
			
			$starttime = strtotime($starttime);
			$endtime = strtotime($endtime);
			$noticetime = strtotime($noticetime);
			if($starttime > $endtime){
				$tmp = $endtime;
				$endtime = $starttime;
				$starttime = $tmp;
			}
			
			if($starttime === FALSE || $endtime === FALSE){
				$this->error("时间格式错误！");
			}
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
				'starttime'=>$starttime,
				'endtime'=>$endtime,
				'noticetime'=>$noticetime,
			);
		
			
			$result = apiCall("Admin/Banners/add", array($entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("保存成功！",U('Admin/Advert/index'));
			
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
			$starttime = I('post.startdatetime',FALSE);
			$endtime = I('post.enddatetime',FALSE);
			$noticetime = I('post.noticedatetime',FALSE);
			$starttime = strtotime($starttime);
			$endtime = strtotime($endtime);
			$noticetime = strtotime($noticetime);
			if($starttime === FALSE || $endtime === FALSE){
				$this->error("时间格式错误！");
			}
						
			if($starttime > $endtime){
				$tmp = $endtime;
				$endtime = $starttime;
				$starttime = $tmp;
			}
			$entity = array(
				'title'=>$title,
				'notes'=>$notes,
				'img'=>I('img',''),
				'url'=>I('url',''),
				'starttime'=>$starttime,
				'endtime'=>$endtime,
				'noticetime'=>$noticetime,
			);
			
			
			$result = apiCall("Admin/Banners/saveByID", array($id,$entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("保存成功！",U('Admin/Advert/index'));
			
		}
	}
		
	public function delete($redirectURL=false){
		
		$id = I('id',0);
		$map = array("id"=>$id);
		$result = apiCall("Admin/Banners/delete", array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->success("删除成功！");
		
	}
	
	

	
}
