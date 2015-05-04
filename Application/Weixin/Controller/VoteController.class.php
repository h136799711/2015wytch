<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Controller;

class VoteController extends WeixinController{
	
	private $perUserMaxTicket = 2;
	private $group = 0;
	private $type = 2;//投票限制判断范围 1: 在每个投票范围内统计 每个投票投了几票做限制 ，2: 在每个投票的选项统计 每个选项投了几票做限制
	
	
	protected function _initialize(){
		parent::_initialize();
		C('SHOW_PAGE_TRACE',false);
		$this -> refreshWxaccount();
//		$debug = false;
//		if($debug){
//			$this->getDebugUser();
//		}else{
//			$url = getCurrentURL();
//			$this->getWxuser($url);
//		}
//		if(empty($this->userinfo)){
//			$this->error("无法获取到用户信息！");
//			exit();
//		}
		$this->getCurrentUser();
		$this->assign("userinfo",$this->userinfo);
		$this->perUserMaxTicket = 2;//每天$perUserMaxTicket票同一ip
		$this->assign("perUserMaxTicket",$this->perUserMaxTicket);
		$this->group = I('get.group',0);
		$this->assign("group",$this->group);
	}
	
	
	public function result(){
		//
		$group = I('group','');
		
		$map = array();
		$map['group'] = $group;
		
		$order = "sort asc";
		$result = apiCall("Weixin/Vote/queryNoPaging",array($map,$order));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$result_arr = array();
//		$this->assign("groups",);
		
		
		$tmpArr = array();
		$sortArr = array();
		foreach($result['info']  as $vo){
			$entity = array(
				'vote_name'=>$vo['vote_name'],
				'sort'=>$vo['sort'],
				'endtime'=>$vo['endtime'],
				'is_end'=>0,//是否结束
				'_total'=>0,//总参与人数
				'_options'=>array(),
			);
			
			if($vo['endtime'] - time() <= 0){
				$entity['is_end'] = 1;
			}
			$sortArr[$vo['sort']] = $vo['id'];
			$result_arr[$vo['id']] = $entity;
			array_push($tmpArr,$vo['id']);						
		}
		if(count($tmpArr) == 0){
			array_push($tmpArr,-1);
		}
		
		unset($map['group']);
		$map['vote_id'] = array('in',$tmpArr);
		$order =  " sort desc ";
		$result = apiCall("Weixin/VoteOption/queryNoPaging", array($map,$order));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		$option_ids = array();
		foreach($result['info'] as $vo){
			$entity = array(
				'option_id'=>$vo['id'],
				'option_name'=>$vo['option_name'],
				'sort'=>$vo['sort'],
				'img_url'=>$vo['img_url'],
				'vote_id'=>$vo['vote_id'],
				'_vote_cnt'=>0 , // 投票统计
				'_rank'=>-1,
			);
			array_push($option_ids,$vo['id']);
			$result_arr[$vo['vote_id']]['_options'][$vo['id']] = $entity;
		}
		
		if(count($option_ids) == 0){
			array_push($option_ids,-1);
		}
		
		//获取选项统计信息
		$result = apiCall("Weixin/VoteOptionResult/voteCount", array($option_ids));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		foreach($result['info'] as $key=>$vo){
			$cnt = intval($vo['cnt']);
			$result_arr[$vo['vote_id']]['_total'] = $result_arr[$vo['vote_id']]['_total'] + $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_vote_cnt'] = $cnt;
			if(!isset($result_arr[$vo['vote_id']]['_options']['_flag_rank'])){
				$result_arr[$vo['vote_id']]['_options']['_flag_rank'] = 0;
			}
			$result_arr[$vo['vote_id']]['_options']['_flag_rank'] = $result_arr[$vo['vote_id']]['_options']['_flag_rank'] + 1;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_rank'] = ($result_arr[$vo['vote_id']]['_options']['_flag_rank']);
		}
		
		ksort($sortArr,SORT_NUMERIC);
		
//		dump($result_arr);
		$result_arr = $this->getTop($result_arr,$i);
//		dump($result_arr);
		$this->assign("resultArr",$result_arr);
		$this->display();
		
	}

	
	public function testindex(){
		if($this->type == 2){
			$curUserOptionCnt =  $this->getEveryOptionVoteCnt($this->userinfo['real_ip'],$this->group);			
		}else{
			$curUserOptionCnt = array();
		}
		//
		$group = I('group','');
		
		$map = array();
		$map['group'] = $group;
		
		$order = "sort asc";
		$result = apiCall("Weixin/Vote/queryNoPaging",array($map,$order));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$result_arr = array();
		
		
		$tmpArr = array();
		$sortArr = array();
		foreach($result['info']  as $vo){
			$entity = array(
				'vote_name'=>$vo['vote_name'],
				'sort'=>$vo['sort'],
				'endtime'=>$vo['endtime'],
				'_total'=>0,//总参与人数
				'_options'=>array(),
				'_cant_vote'=>0,//默认可以投票
			);
			if($vo['endtime'] - time() <= 0){
				$entity['is_end'] = 1;
			}
			
			if($this->type == 1){
				//获取单人可投票限制
				if(!$this->checkMaxTicket($vo['id'],'',$this->userinfo['real_ip'])){
					$entity['_cant_vote'] = 1;//不能投票
				}
			}
			$sortArr[$vo['sort']] = $vo['id'];
			$result_arr[$vo['id']] = $entity;
			
			array_push($tmpArr,$vo['id']);						
		}
		if(count($tmpArr) == 0){
			array_push($tmpArr,-1);
		}
		
		unset($map['group']);
		$map['vote_id'] = array('in',$tmpArr);
		$order =  " id asc ";
		$result = apiCall("Weixin/VoteOption/queryNoPaging", array($map,$order));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		$option_ids = array();
		foreach($result['info'] as $vo){
			$entity = array(
				'option_id'=>$vo['id'],
				'option_name'=>$vo['option_name'],
				'sort'=>$vo['sort'],
				'img_url'=>$vo['img_url'],
				'vote_id'=>$vo['vote_id'],
				'_vote_cnt'=>0 , // 投票统计
				'_rank'=>0,
				'_limit'=>0,
			);
			
			if(isset($curUserOptionCnt[$vo['id']])){
				if(intval($curUserOptionCnt[$vo['id']]['option_cnt']) >= $this->perUserMaxTicket){
					$entity['_limit'] = 1;
				}
			}
			
			array_push($option_ids,$vo['id']);
			$result_arr[$vo['vote_id']]['_options'][$vo['id']] = $entity;
		}
		
		if(count($option_ids) == 0){
			array_push($option_ids,-1);
		}
		
		//获取选项统计信息
		$result = apiCall("Weixin/VoteOptionResult/voteCount", array($option_ids));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		foreach($result['info'] as $key=>$vo){
			$cnt = intval($vo['cnt']);
			$result_arr[$vo['vote_id']]['_total'] = $result_arr[$vo['vote_id']]['_total'] + $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_vote_cnt'] = $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_rank'] = $key;
		}
		
		ksort($sortArr,SORT_NUMERIC);
		
		foreach($result_arr as &$vo){
			
			//TODO: 对其进行排序
			
			$list = &$vo['_options'];
			
			
		}
		
		$this->assign("sortArr",$sortArr);
		$this->assign("resultArr",$result_arr);
		$this->display();	
	}
	
	
	
	
	public function index(){
		if($this->type == 2){
			$curUserOptionCnt =  $this->getEveryOptionVoteCnt($this->userinfo['real_ip'],$this->group);			
		}else{
			$curUserOptionCnt = array();
		}
		//
		$group = I('group','');
		
		$map = array();
		$map['group'] = $group;
		
		$order = "sort asc";
		$result = apiCall("Weixin/Vote/queryNoPaging",array($map,$order));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$result_arr = array();
		
		
		$tmpArr = array();
		$sortArr = array();
		$currentTime = time();
		foreach($result['info']  as $vo){
			$entity = array(
				'vote_name'=>$vo['vote_name'],
				'sort'=>$vo['sort'],
				'endtime'=>intval($vo['endtime']),
				'starttime'=>intval($vo['starttime']),
				'_total'=>0,//总参与人数
				'_options'=>array(),
				'_cant_vote'=>0,//默认可以投票
				'_is_start'=>0,//是否已经开始,默认没有
				'_count_time'=>0,
			);
			if($entity['endtime'] - $currentTime <= 0){
				$entity['is_end'] = 1;
			}
			if($entity['starttime'] - $currentTime <= 0){//开始时间小于当前时间则已经开始
				$entity['_is_start'] = 1;
			}else{
				$entity['_count_time'] = $entity['starttime'] - $currentTime;
			}
			
			if($this->type == 1){
				//获取单人可投票限制
				if(!$this->checkMaxTicket($vo['id'],'',$this->userinfo['real_ip'])){
					$entity['_cant_vote'] = 1;//不能投票
				}
			}
			$sortArr[$vo['sort']] = $vo['id'];
			$result_arr[$vo['id']] = $entity;
			
			array_push($tmpArr,$vo['id']);						
		}
		if(count($tmpArr) == 0){
			array_push($tmpArr,-1);
		}
		
		unset($map['group']);
		$map['vote_id'] = array('in',$tmpArr);
		$order =  " id asc ";
		$result = apiCall("Weixin/VoteOption/queryNoPaging", array($map,$order));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		$option_ids = array();
		foreach($result['info'] as $vo){
			$entity = array(
				'option_id'=>$vo['id'],
				'option_name'=>$vo['option_name'],
				'sort'=>$vo['sort'],
				'img_url'=>$vo['img_url'],
				'vote_id'=>$vo['vote_id'],
				'_vote_cnt'=>0 , // 投票统计
				'_rank'=>0,
				'_limit'=>0,
			);
			
			if(isset($curUserOptionCnt[$vo['id']])){
				if(intval($curUserOptionCnt[$vo['id']]['option_cnt']) >= $this->perUserMaxTicket){
					$entity['_limit'] = 1;
				}
			}
			
			array_push($option_ids,$vo['id']);
			$result_arr[$vo['vote_id']]['_options'][$vo['id']] = $entity;
		}
		
		if(count($option_ids) == 0){
			array_push($option_ids,-1);
		}
		
		//获取选项统计信息
		$result = apiCall("Weixin/VoteOptionResult/voteCount", array($option_ids));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		foreach($result['info'] as $key=>$vo){
			$cnt = intval($vo['cnt']);
			$result_arr[$vo['vote_id']]['_total'] = $result_arr[$vo['vote_id']]['_total'] + $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_vote_cnt'] = $cnt;
			$result_arr[$vo['vote_id']]['_options'][$vo['option_id']]['_rank'] = $key;
		}
		
		ksort($sortArr,SORT_NUMERIC);
		
		foreach($result_arr as &$vo){
			
			//TODO: 对其进行排序
			
			$list = &$vo['_options'];
			
			
		}
		
		$this->assign("sortArr",$sortArr);
		$this->assign("resultArr",$result_arr);
		$this->display();	
	}
	
	
	
	
	
	
	
	
	private function getTop($result_arr,$lastRank) {
		$result = array();
		
		foreach($result_arr as $vo){
			$entity = array(
				'vote_name'=>$vo['vote_name'],
				'sort'=>$vo['sort'],
				'is_end'=>$vo['is_end'],//是否结束
				'_total'=>$vo['_total'],//总参与人数
				'_top_options'=>array(),
			);
			$maxRank = $vo['_options']['_flag_rank']+1;
			$extra = $vo['_options']['_flag_rank']+1;
			$top = count($vo['_options']);
			foreach($vo['_options'] as $key=>$option){
				if($key == '_flag_rank'){
					continue;
				}
				if($option['_rank'] == -1){
					$option['_rank'] = $maxRank;					
					$maxRank = $maxRank + 1;
					
					
					if($extra <= $top){
						$entity['_top_options'][$extra] = $option;
						$extra = $extra + 1;
					}
					
				}elseif($option['_rank'] <= $top){
					$entity['_top_options'][$option['_rank']] = $option;
				}
			}
			ksort($entity['_top_options'],SORT_NUMERIC);
			array_push($result,$entity);
		}
		
		return $result;
		
	}
	
	
	
	
	
	
	

	
	/**
	 * 添加投票结果
	 */
	public function addResult(){
		if(IS_POST){
			
//			$perUserMaxTicket = 2;//每天$perUserMaxTicket票同一ip
			
			$wxuser_id = $this->userinfo['id'];
			$real_ip = $this->userinfo['real_ip'];
			$option_id = I('get.option_id',0,'intval');
			$vote_id = I('get.vote_id',0,'intval');
			
			if($option_id == 0 || $vote_id == 0){
				$this->error("操场失败！");
			}
			
			$entity = array(
				'wxuser_id'=>$wxuser_id,
				'real_ip'=> $real_ip,
				'option_id'=>$option_id,
				'vote_id'=>$vote_id,
				'group'=>$this->group,
			);
			
			if($this->checkIsEnd($vote_id)){
				$this->error("当前投票已结束！");
			}
			
			if(!$this->checkMaxTicket($vote_id, $option_id, $real_ip)){
				$this->error("感谢您的支持，请明天再来投！");
			}
			
//			$map = array();
//			$map['option_id'] = $option_id;
//			$map['vote_id'] = $vote_id;
//			$map['real_ip'] = $real_ip;
//			$today = date("Y-m-d",time());
//			
//			$today = strtotime($today);
//			$map['vote_time'] = array(array('lt',time()),array('gt',$today));
//			
//			//TODO: 判断用户 投了几票 ，做限制			
//			$result = apiCall("Weixin/VoteOptionResult/count", array($map));
//			
//			if(!$result['status']){
//				$this->error($result['info']);
//			}
//			$cnt = intval($result['info']);
//			
//			if($perUserMaxTicket - $cnt <= 0){
//			}
			$result = apiCall("Weixin/VoteOptionResult/add",array($entity));
			
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			
			$this->success("感谢您的一票！");
		}else{
			$this->error("禁止访问！");
		}
		
	}
	
	//===PRIVATE======
	
//	public function test(){
//		$entity = array(
//		  "wxuser_id" => (0),
//		  "option_id" => (6),
//		  "vote_id" => (6),
//		  "group" => "20150503",
//		  "group_id" => "20150503",
//		  "real_ip" => 1943560795,
//		);
//		$result = apiCall("Weixin/VoteOptionResult/add",array($entity));
//		dump($result);
//		
//	}
//	
	
	/**
	 * 检测投票是否已结束
	 */
	private function checkIsEnd($vote_id){
		$map = array(
			'id'=>$vote_id
		);
		$result = apiCall("Weixin/Vote/getInfo", array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		if(is_null($result['info']) ){
			return true;
		}
		
		$endtime = intval($result['info']['endtime']);
		
		
		if(time() - $endtime > 0){
			return true;	
		}
		
		
		return false;
	}
	
	/**
	 * 获取单个用户，当天对每个选项的投票数情况
	 */
	private function getEveryOptionVoteCnt($real_ip,$group){
		$map = array(
			'real_ip' =>$real_ip,
			'group'=>$group
		);
		
		$today = date("Y-m-d",time());
		
		$today = strtotime($today);
		$map['vote_time'] = array(array('lt',time()),array('gt',$today));
		
		$result =  apiCall("Weixin/VoteOptionResult/myVoteCount", array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		$tmp_arr = array();
		foreach($result['info'] as $vo){
			$tmp_arr[$vo['option_id']] = $vo;
		}

		return $tmp_arr;
		
	}
	
	/**
	 * 检测用户是否投票数是否达到限制
	 * 1. 每个分组
	 * 2. 每天
	 * 3. 投票限制
	 */
	private function checkMaxTicket($vote_id,$option_id,$real_ip){
		$map = array();
		if($this->type == 2){
			$map['option_id'] = $option_id;
		}
		$map['vote_id'] = $vote_id;
		$map['real_ip'] = $real_ip;
		$today = date("Y-m-d",time());
		
		$today = strtotime($today);
		$map['vote_time'] = array(array('lt',time()),array('gt',$today));
		
		//TODO: 判断用户 投了几票 ，做限制			
		$result = apiCall("Weixin/VoteOptionResult/count", array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		$cnt = intval($result['info']);
		
		if($this->perUserMaxTicket - $cnt <= 0){
			return false;
		}
		return true;
	}
	


	private function getCurrentUser(){
		$this->userinfo = array(
			'real_ip'=>ip2long(get_client_ip()),
			'id'=>0,
		);
	}
	
	/**
	 * 刷新
	 */
	private function refreshWxaccount() {
		$token = I('get.token', '');
		if (!empty($token)) {
			session("shop_token", $token);
		} elseif (session("?shop_token")) {
			$token = session("shop_token");
		}
		if(empty($token)){
			$token = C('WEIXIN_TOKEN');
		}
		$result = apiCall('Weixin/Wxaccount/getInfo', array( array('token' => $token)));
		if ($result['status'] && is_array($result['info'])) {
			$this -> wxaccount = $result['info'];
			$this -> wxapi = new \Common\Api\WeixinApi($this -> wxaccount['appid'], $this -> wxaccount['appsecret']);
		} else {
			exit("公众号信息获取失败，请重试！");
		}
	}
	
}
