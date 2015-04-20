<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class CategorySkuController extends AdminController{
	
	protected $level;
	protected $parent;
	protected $preparent;
	
	protected function _initialize(){
		parent::_initialize();
		$this->level = I('level',0);
		$this->parent = I('parent',0);
		$this->preparent = I('preparent',0);
		
		$this->assign("level",$this->level );
		$this->assign("parent",$this->parent );
		$this->assign("preparent",$this->preparent );
	}
	
	public function index(){
		
		$cate_id = I('cate_id',-1);
		$map = array(
			'cate_id'=>$cate_id
		);
		$name = I('name','');
		$params = array(			
			'cate_id'=>$cate_id
		);
		
				
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		
		$order = " id asc ";
		//
		$result = apiCall("Admin/Sku/query",array($map,$page,$order,$params));
		
		//
		if($result['status']){
			$this->assign('cate_id',$cate_id);
			$this->assign('show',$result['info']['show']);
			$this->assign('list',$result['info']['list']);
			$this->display();
		}else{
			LogRecord('INFO:'.$result['info'],'[FILE] '.__FILE__.' [LINE] '.__LINE__);
			$this->error(L('UNKNOWN_ERR'));
		}
	}
	
	public function add(){
		$cate_id = I('cate_id',-1);
		if(IS_GET){
			
			$this->assign('cate_id',$cate_id);
			$this->display();
		}else{
			$name = I('name','');
			
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			
			$entity = array(
				'cate_id'=>$cate_id,
				'name'=>$name,
				'sku_id'=>'custom'
			);
			
			$result = apiCall("Admin/Sku/add",array($entity));
			
			
			if($result['status']){
				$this->success("添加成功！",U('Admin/CategorySku/index',array('cate_id'=>$cate_id,'preparent'=>$preparent,'parent'=>$parent,'level'=>$level)));
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}

	public function edit(){
		$cate_id = I('cate_id',-1);
		$id = I('id','');
		
		if(IS_GET){
			$result = apiCall("Admin/Sku/getInfo",array(array('id'=>$id)));
			if($result['status']){
				$this->assign("vo",$result['info']);
			}
			$this->assign('cate_id',$cate_id);
			$this->display();
		}else{
			$name = I('name','');
			
			if(empty($name)){
				$this->error("属性不能为空！");
			}
			
			$entity = array(
				'name'=>$name,
				'sku_id'=>'custom'
			);
			
			$result = apiCall("Admin/Sku/saveByID",array($id,$entity));
			
			
			if($result['status']){
				$this->success("保存成功！",U('Admin/CategorySku/index',array('cate_id'=>$cate_id,'preparent'=>$preparent,'parent'=>$parent,'level'=>$level)));
			}
			else{
				$this->error($result['info']);
			}
			
		}
	}

	public function delete(){
		
		$id = I('get.id',0);
		$map = array('sku_id'=>$id);
		$result = apiCall("Admin/Skuvalue/queryNoPaging",array($map));
		if($result['status']){
			if(count($result['info']) > 0){
				$this->error("存在SKU值，请先删除SKU值！");				
			}
		
			$result = apiCall("Admin/Sku/delete",array(array('id'=>$id)));
			if($result['status']){
				$this->success("删除成功！");
			}else{
				$this->error($result['info']);
			}
			
		}else{
			
		}
	}

	
}
