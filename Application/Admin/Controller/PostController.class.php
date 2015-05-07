<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class PostController extends  AdminController{
	
	public function index(){
		//get.startdatetime
		$startdatetime = I('startdatetime',date('Y/m/d H:i',time()-24*3600),'urldecode');
		$enddatetime = I('enddatetime',date('Y/m/d H:i',time()+24*3600),'urldecode');
		
		//分页时带参数get参数
		$params = array(
			'startdatetime'=>$startdatetime,
			'enddatetime'=>$enddatetime
		);
		
		$startdatetime = strtotime($startdatetime);
		$enddatetime = strtotime($enddatetime);
				
		if($startdatetime === FALSE || $enddatetime === FALSE){
			LogRecord('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('ERR_DATE_INVALID'));
		}
		
		$map = array();
		
		$map['post_modified'] = array(array('EGT',$startdatetime),array('elt',$enddatetime),'and'); 
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " post_modified desc ";
		//
		$result = apiCall('Admin/Post/query',array($map,$page,$order,$params));
		//
		if($result['status']){
			$this->assign('startdatetime',$startdatetime);
			$this->assign('enddatetime',$enddatetime);
			$this->assign('show',$result['info']['show']);
			$this->assign('list',$result['info']['list']);
			$this->display();
		}else{
			LogRecord('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
	}
	
	
	public function add(){
		if(IS_GET){
			
			$this->display();
		}else{
			$post_category = I('post.post_category',20);

			$entity = array(
				'main_img'=>I('post.main_img',''),
				'post_category'=>$post_category,
				'post_content'=>I('post_content',''),
				'post_excerpt'=>I('post_excerpt',''),
				'post_title'=>I('post_title',''),
				'post_author'=>UID,
				'post_status'=>I('post_status','draft'),
				'comment_status'=>I('commen_status','closed'),
				'post_parent'=>0,
				'post_type'=>'post_type',
				'comment_count'=>0
			);
			
			$result = apiCall("Admin/Post/add", array($entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("操作成功！",U('Admin/Post/index'));
			
		}
	}
	
	public function edit(){
		$id = I('id',0);
		if(IS_GET){
			$result = apiCall("Admin/Post/getInfo", array(array("id"=>$id)));
			if(!$result['info']){
				$this->error($result['info']);
			}
			$this->assign("vo",$result['info']);
			$this->display();
		}else{
			$post_category = I('post.post_category',20);
			
			$entity = array(
				'main_img'=>I('post.main_img',''),
				'post_category'=>$post_category,
				'post_content'=>I('post_content',''),
				'post_excerpt'=>I('post_excerpt',''),
				'post_title'=>I('post_title',''),
				'post_status'=>I('post_status','draft'),
				'comment_status'=>I('commen_status','closed'),
			);
			$result = apiCall("Admin/Post/saveByID", array($id,$entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("保存成功！",U('Admin/Post/index'));
			
		}
	}
	
	public function delete(){
		$id = I('id',0);
		
		$result = apiCall("Admin/Post/delete", array(array("id"=>$id)));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->success("删除成功！");
	
	}
	
}
