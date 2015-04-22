<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class DatatreeController extends AdminController{
	private $parent;
	private $preparent;
	
	protected function _initialize(){
		parent::_initialize();
		
		$this->parent = I('parent',0);
		
		$result = apiCall("Admin/Datatree/getInfo",array(array('id'=>$this->parent)));
		if(!$result['status']){
			$this->error($result['info']);
		}

		if(is_array($result['info'])){
			$this->preparent = $result['info']['parentid'];
		}
		$this->assign('parent',$this->parent);
		$this->assign('preparent',$this->preparent);
	}
		
	public function index(){
		$name = I('name','');
		$map = array('parentid'=>$this->parent);
		
		$params = array('parent'=>$this->parent);
		
		if(!empty($name)){
			$map['name'] = array('like',"%$name%");
			$params['name'] = $name;
		}
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		
		$order = " sort desc ";
		//
		$result = apiCall("Admin/Datatree/query",array($map,$page,$order,$params));
		
		//
		if($result['status']){
			$this->assign('name',$name);
			$this->assign('show',$result['info']['show']);
			$this->assign('list',$result['info']['list']);
			$this->display();
		}else{
			LogRecord('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
		
	}	


	public function search(){
		
		$name = I('name','');
		$map = array();
		
		$params = array('parent'=>$this->parent);
		
		
		if(!empty($name)){
			$map['name'] = array('like',"%$name%");
			$params['name'] = $name;
		}
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		
		$order = " sort desc ";
		//
		$result = apiCall("Admin/Datatree/query",array($map,$page,$order,$params));
		
		//
		if($result['status']){
			$this->assign('name',$name);
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
			$result = apiCall("Admin/Datatree/getInfo",array(array('id'=>$this->parent)));
			$level = 0;
			$parents = $this->parent.',';
			if($result['status'] && is_array($result['info'])){
				$level = intval($result['info']['level'])+1;
				$parents = $result['info']['parents'].$parents;
			}
			$entity = array(
				'name'=>I('name',''),
				'notes'=>I('notes',''),
				'sort'=>I('sort',''),
				'level'=>$level,
				'parents'=>$parents,
				'parentid'=>$this->parent,
				'code'=>I('code',''),
				'iconurl'=>I('iconurl',''),
			);
			
			$result = apiCall("Admin/Datatree/add", array($entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}

			$this->success("操作成功！",U('Admin/Datatree/index',array('parent'=>$this->parent)));
			
		}
	}
	
	public function delete(){
		$id = I('id',0);
		
		$result = apiCall("Admin/Datatree/queryNoPaging", array(array('parentid'=>$id)));
		if(!$result['status']){
			$this->error($result['info']);
		}
		if(is_array($result['info']) && count($result['info']) > 0){
			$this->error("有子级，请先删除所有子级！");
		}
		
		$result = apiCall("Admin/Datatree/delete", array(array('id'=>$id)));
		if(!$result['status']){
			$this->error($result['info']);
		}

		$this->success("操作成功！");
		
		
	}
	
	public function edit(){
		$id = I('id',0);
		if(IS_GET){
			$result = apiCall("Admin/Datatree/getInfo",array(array('id'=>$id)));
			if($result['status']){
				$this->assign("vo",$result['info']);
			}
			
			$this->display();
		}else{
			
			$entity = array(
				'name'=>I('name',''),
				'notes'=>I('notes',''),
				'sort'=>I('sort',''),
				'code'=>I('code',''),
				'iconurl'=>I('iconurl',''),
			);
			$result = apiCall("Admin/Datatree/saveByID", array($id,$entity));
			
			if(!$result['status']){
				$this->error($result['info']);
			}

			$this->success("操作成功！",U('Admin/Datatree/index',array('parent'=>$this->parent)));
			
		}
	}
	
	
}

