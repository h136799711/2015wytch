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
}
