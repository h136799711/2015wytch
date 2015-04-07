<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Api;

use Ucenter\Model\AuthGroupAccessModel;

class AuthGroupAccessApi extends \Common\Api\Api{
	
	protected function _init(){
		$this->model = new AuthGroupAccessModel();
	}
	
	/**
     * 把用户添加到用户组,支持批量添加用户到用户组
	 * @param $uid 用户id
	 * @param $groupid 用户组id
     * 示例: 把uid=1的用户添加到group_id为1,2的组
	 * 1. AuthGroupModel->addToGroup(1,'1,2');
	 * 2. AuthGroupModel->addToGroup(1,array('1','2'));
	 * @return 返回最后插入的主键id
	 */
	public function addToGroup($uid,$groupid){
		$member = new MemberApi();
		$result = $member->getInfo(array('uid'=>$uid));
		if($result['status'] === false){
			return $this->apiReturnErr("编号为{$uid}的用户不存在！");
        }
		
		
        $groupid = is_array($groupid)?$groupid:explode( ',',trim($groupid,',') );
		
		if(count($groupid) > 1){
			//批量添加时，删除旧数据
			$this->delete(array("uid"=>$uid));
		}else{
			$result = $this->getInfo(array('group_id'=>$groupid[0],'uid'=>$uid));
			
			if($result['status'] && is_array($result['info'])){
				return $this->apiReturnErr("已经添加过了！");
			}elseif($result['status'] === false){
				return $this->apiReturnErr($result['info']);
			}
		}			
		
        foreach ($groupid as $g){
            if( is_numeric($uid) && is_numeric($g) ){
                $listEntity[] = array('group_id'=>$g,'uid'=>$uid);
            }
        }
		
		$result = $this->model->addAll($listEntity);
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
        }else{
			return $this->apiReturnSuc($result);
        }
	}

	
}
