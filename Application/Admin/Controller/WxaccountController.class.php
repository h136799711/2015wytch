<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class WxaccountController extends AdminController{
	
	
	protected function _initialize(){
		parent::_initialize();
			
	}
	
	public function set(){
		$id = I('get.id',0);
		session("wxaccountid",$id);
		
		$this->success("操作成功！");
	}
	
	public function change(){
		$map = array('uid'=>UID);
		$page = array('curpage'=>I('get.p',0),'size'=>C("LIST_ROWS"));
		$params = array();
		$list = apiCall("Admin/Wxaccount/query", array($map,$page,"createtime desc",$params));
		if($list['status']){
			$this->assign("list",$list['info']['list']);
			$this->assign("show",$list['info']['show']);
			$this->display();
		}
	}
	
	/**
	 * 首次关注时响应关键词管理
	 */
	public function saveFirstResp(){
		$keyword = I('post.ss_keyword','');			
		$config = array("SS_KEYWORD"=>$keyword);
		$result = apiCall("Admin/Config/set", array($config));
		if($result['status']){
			C('SS_KEYWORD',$keyword);
			$this->success(L('RESULT_SUCCESS'));
		}else{
			$this->error($result['info']);
		}
	}
	
	/**
	 * 公众号帮助信息
	 */
	public function help(){
		
		if(IS_GET){
			$map = array('id'=>getWxAccountID());
			$result = apiCall("Admin/Wxaccount/getInfo",array($map));
			
			if($result['status']){
				$this->assign("wxaccount",$result['info']);
				$this->display();
			}else{
				LogRecord($result['info'], __FILE__);
				$this->error($result['info']);
			}
		}
	}
	
	/**
	 * 微信账号信息编辑
	 */
	public function edit(){
		if(IS_GET){
			$map = array('id'=>getWxAccountID());
			$result = apiCall("Admin/Wxaccount/getInfo",array($map));
			if($result['status']){
				$this->assign("wxaccount",$result['info']);
				$this->display();
			}else{
				LogRecord($result['info'], __FILE__);
				$this->error($result['info']);
			}
		}
	}
	
	public function store(){
		if(IS_POST){
			import("Org.String");
			$id= I('post.id',0,'intval');
			$len = 43;
			$EncodingAESKey= I('post.encodingAESKey','');
			if(empty($EncodingAESKey)){
        			$EncodingAESKey =  \String::randString($len,0,'0123456789');
			}
       	 	$tokenvalue = \String::randString(8,3);	
			$entity = array(
				'wxname'=>I('post.wxname',''),
				'appid'=>I('post.appid'),
				'appsecret'=>I('post.appsecret'),
//				'token'=>I('post.token'),
				'weixin'=>I('post.weixin'),
				'headerpic'=>I('post.headerpic',''),
				'qrcode'=>I('post.qrcode',''),
				'wxuid'=>I('post.wxuid'),
//				'uid'=>UID,
				'encodingAESKey'=>$EncodingAESKey,
			);
			
			if(!empty($id) && $id > 0){
//				dump("save");
//				$entity['id'] = $id;
				$result = apiCall('Admin/Wxaccount/saveByID', array($id, $entity));
				if ($result['status'] === false) {
					LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
					$this -> error($result['info']);
				} else {
					$this -> success(L('RESULT_SUCCESS'), U('Admin/Wxaccount/edit'));
				}
			}else{
				$entity['uid'] = UID;
				$entity['token'] = $tokenvalue.time();			
				$result = apiCall('Admin/Wxaccount/add', array($entity));
				if ($result['status'] === false) {
					LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
					$this -> error($result['info']);
				} else {
					$this -> success(L('RESULT_SUCCESS'),  U('Admin/Wxaccount/edit'));
				}
			}
		}
	}
	
	//private
	private function randToken($length){
		$token = '';
		$strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($strPol)-1;
		for($i=0;$i<$length;$i++){
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}
		
		return $token;
	}

	
	
}
