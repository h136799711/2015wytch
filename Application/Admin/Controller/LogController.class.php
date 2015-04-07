<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class LogController extends AdminController{
	
	
	protected function _initialize(){
		parent::_initialize();
		$this->assignTitle('日志管理');
	}
	
	public function index(){
		
		//get.startdatetime
		$startdatetime = I('startdatetime',date('Y/m/d H:i',time()-24*3600),'urldecode');
		$enddatetime = I('enddatetime',date('Y/m/d H:i',time()),'urldecode');
		
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
		
		$map['timestamp'] = array(array('EGT',$startdatetime),array('elt',$enddatetime),'and'); 
		
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " timestamp desc ";
		//
		$result = apiCall('Admin/Log/query',array($map,$page,$order,$params));
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
	
	public function system(){
		$this->index();
	}
	
	
	
}
