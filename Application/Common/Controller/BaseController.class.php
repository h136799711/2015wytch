<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Controller;
use Think\Controller;

class BaseController extends Controller{
		
	protected $seo = array(
			'title'=>'',
			'keywords'=>'',
			'description'=>'',
	);
	
	protected $cfg = array(
			'owner'=>'',
			'theme'=>'simplex'
	);
	/*
	 * Seo 配置
	 * */
	public function assignVars($seo=array('title'=>'标题','keywords'=>'关键词','description'=>'描述',),	$cfg=array('owner'=>'绍兴古睿信息科技有限公司')){
		$this->seo = array_merge($this->seo,$seo);
		$this->cfg = array_merge($this->cfg,$cfg);
		
		$this->assign("seo",$this->seo);
		$this->assign("cfg",$this->cfg);
	}
	/**
	 * 赋值页面标题值
	 */
	public function assignTitle($title){
		$this->seo = array_merge($this->seo,array('title'=>$title));
		$this->assign("seo",$this->seo);
	}
	
	//初始化
	protected function _initialize(){
		//设置程序版本
		
		$this->assignVars();
	}
		
	/* 空操作，用于输出404页面 */
    protected function _empty() {
    	header('HTTP/1.1 404 Not Found'); 
		header("status: 404 Not Found");     	
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		if(!defined('DEBUG')){
			header('Location: '.__ROOT__. '/Public/404.html');
		}else{
			echo '{"status": "404","msg": "resource not found!"}';
			exit();
		}
//      redirect(__ROOT__ . '/Public/404.html', 0, '请求资源不存在！');
    }
}
