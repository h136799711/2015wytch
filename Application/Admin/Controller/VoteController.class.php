<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class VoteController extends AdminController{
	
	public function index(){
		
		$map = array();
		$page = array('curpage'=>I('get.p',0),'size'=>C('LIST_ROW'));
		$order = " sort asc ";
		$result = apiCall("Admin/Vote/query",array($map,$page,$order));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->assign("list",$result['info']['list']);
		$this->assign("show",$result['info']['show']);
		$this->display();
		
	}
	
	/**
	 * 查看投票结果
	 */
	public function result(){
		
		$this->display();
	}
	
	
	public function add(){
		if(IS_GET){
			$this->assign("startdatetime",time());
			$this->assign("enddatetime",time()+7*24*3600);
			$this->display();
		}else{
			$enddatetime = I('post.enddatetime',time(),'strtotime');
			$startdatetime = I('post.startdatetime',time(),'strtotime');
			
//			dump($enddatetime);
//			exit();
			$vote_name = I('post.vote_name','');
			$entity = array(
				'sort'=>I('post.sort',0),
				'group'=>I('post.group',0),
				'vote_name'=>$vote_name,
				'endtime'=>$enddatetime,
				'starttime'=>$startdatetime,
				'text'=>'',
			);
			
			$result = apiCall("Admin/Vote/add",array($entity));
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->success("添加成功",U('Admin/Vote/index'));
		}
	}
	
	public function delete(){
		
		$id = I('get.id',0);
		
		$result = apiCall("Admin/VoteOption/query",array(array('vote_id'=>$id)));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		if(is_array($result['info']['list'])){
			$this->error("不能删除此投票，请先删除投票的选项！");
		}
		
		
		$result = apiCall("Admin/Vote/delete",array(array('id'=>$id)));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->success("删除成功",U('Admin/Vote/index'));
		
	}
	
	public function edit(){
		
		$id = I('get.id',0);
		if(IS_GET){
		
			$result = apiCall("Admin/Vote/getInfo",array(array('id'=>$id)));
				
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->assign("vo",$result['info']);
			$this->display();

		}else{
			$id = I('post.id',0);
			
			$startdatetime = I('post.startdatetime',time(),'strtotime');
			$enddatetime = I('post.enddatetime',time(),'strtotime');
			$vote_name = I('post.vote_name','');
			
			$entity = array(
				'vote_name'=>$vote_name,
				'endtime'=>$enddatetime,
				'starttime'=>$startdatetime,
				'sort'=>I('post.sort',0),
				'group'=>I('post.group',0),
				'text'=>'',
			);
			
			
			$result = apiCall("Admin/Vote/saveByID",array($id,$entity));
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->success("保存成功",U('Admin/Vote/index'));
			
		}
		
		
	}
	
	
}

