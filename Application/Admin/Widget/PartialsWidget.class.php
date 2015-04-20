<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Widget;
use Admin\Controller\AdminController;

class PartialsWidget extends AdminController{
	/**
	 * 配置部分内容
	 */
	public function config_set($group){
		$map = array('group'=>$group);
		$result = apiCall('Admin/Config/queryNoPaging',array($map));
		if($result['status']){
			$this->assign("list",$result['info']);
			echo $this->fetch("Widget/config_set");
		}else{
			LogRecord($result['info'], "[INFO]:".__FILE__." [LINE]".__LINE__);
			echo L('ERR_SYSTEM_BUSY');
		}
	}
	
	/**
	 * 数据字典
	 */
	public function datatree($parent,$hasChildren,$checkedID=0){
		if($hasChildren){
			$map['parents'] = array('like','%'.$parent.',%');
			$result = apiCall('Admin/Datatree/queryNoPaging',array($map));
			$tree = new \Common\Model\TreeModel();
			$list = $tree -> toFormatTree($result['info'],'name','id','parentid',$parent);
		}else{
			$map = array('parentid'=>$parent);
			$result = apiCall('Admin/Datatree/queryNoPaging',array($map));
			$list = $result['info'];
		}
		if($result['status']){
			$this->assign("checkedID",$checkedID);
			$this->assign("list",$list);
			echo $this->fetch("Widget/datatree");
		}else{
			LogRecord($result['info'], "[INFO]:".__FILE__." [LINE]".__LINE__);
			echo L('ERR_SYSTEM_BUSY');
		}
		
	}
	
}
