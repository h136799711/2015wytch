<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Admin\Controller;

class CategoryPropvalueController extends AdminController{
	
	private $cate_id;
	
	protected function _initialize(){
		parent::_initialize();
		$this->cate_id = I('cate_id',-1);
		$this->assign("cate_id",$this->cate_id);
	}
	
	public function index(){
		$prop_id = I('prop_id',-1);
		$map = array(
			'prop_id'=>$prop_id
		);
		$name = I('name','');
		$params = array(			
			'prop_id'=>$prop_id
		);
		
				
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		
		$order = " id asc ";
		//
		$result = apiCall("Admin/CategoryPropvalue/query",array($map,$page,$order,$params));
		
		//
		if($result['status']){
			$this->assign('prop_id',$prop_id);
			$this->assign('show',$result['info']['show']);
			$this->assign('list',$result['info']['list']);
			$this->display();
		}else{
			LogRecord('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
	}
	
	public function add(){
		$prop_id = I('prop_id',-1);
		if(IS_GET){
			
			$this->assign('prop_id',$prop_id);
			$this->display();
		}else{
			$name = I('name','');
			
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			
			$entity = array(
				'prop_id'=>$prop_id,
				'valuename'=>$name,
				'propvalueid'=>'custom'
			);
			
			$result = apiCall("Admin/CategoryPropvalue/add",array($entity));
			
			
			if($result['status']){
				$this->success("添加成功！",U('Admin/CategoryPropvalue/index',array('prop_id'=>$prop_id,'cate_id'=>$this->cate_id)));
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}
	
	public function edit(){
		$prop_id = I('prop_id',-1);
		$id = I('id','');
		
		if(IS_GET){
			$result = apiCall("Admin/CategoryPropvalue/getInfo",array(array('id'=>$id)));
			if($result['status']){
				$this->assign("vo",$result['info']);
			}
			$this->assign('prop_id',$prop_id);
			$this->display();
		}else{
			$name = I('name','');
			
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			
			$entity = array(
				'valuename'=>$name,
				'propvalueid'=>'custom'
			);
			
			$result = apiCall("Admin/CategoryPropvalue/saveByID",array($id,$entity));
						
			if($result['status']){
				$this->success("保存成功！",U('Admin/CategoryPropvalue/index',array('prop_id'=>$prop_id,'cate_id'=>$this->cate_id)));
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}

	
	public function delete(){
		$id = I('get.id',0);

		$result = apiCall("Admin/CategoryPropvalue/delete",array(array('id'=>$id)));
		if($result['status']){
			$this->success("删除成功！");
		}else{
			$this->error($result['info']);
		}
		
	}

	
	
	
	
}

