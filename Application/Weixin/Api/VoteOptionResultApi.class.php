<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Weixin\Api;
use \Common\Api\Api;
use \Common\Model\VoteOptionResultModel;

class VoteOptionResultApi extends Api{
	protected function _init(){
		$this->model = new VoteOptionResultModel();
	}
	
	/**
	 * 单人投票数
	 * 
	 */
	public function myVoteCount($map){		
		$result = $this->model->field("count(option_id) as option_cnt,vote_id ,option_id")->where($map)->group("option_id,vote_id")->order(' option_cnt desc')->select();
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}
		
		return $this->apiReturnSuc($result);
	}
	
	/**
	 * 各选项投票人数统计
	 */
	public function voteCount($option_ids){
		$map = array();
		
		$map['option_id'] = array('in',$option_ids);
		$result = $this->model->field("option_id,vote_id ,count(real_ip) as cnt")->where($map)->group("option_id,vote_id")->order(' cnt desc')->select();
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}
		
		return $this->apiReturnSuc($result);
		
	}
}

