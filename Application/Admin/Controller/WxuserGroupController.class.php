<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class WxuserGroupController extends  AdminController {
	protected function _initialize() {
		parent::_initialize();
	}

	public function index() {

		$map = array();

		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " id asc ";
		//
		$result = apiCall('Admin/WxuserGroup/query', array($map, $page, $order));

		//
		if ($result['status']) {
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	public function add() {
		if (IS_GET) {
			$this -> display();
		} elseif (IS_POST) {
			$entity = array('name' => I('post.name', ''), 'description' => I('post.description', ' '), );

			$result = apiCall('Admin/WxuserGroup/addWithAccess', array($entity));
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

		}
	}

	public function edit() {
		if (IS_GET) {

			$id = I('get.id', 0);

			$result = apiCall('Admin/WxuserGroup/getInfo', array($id, $entity));
			if ($result['status']) {
				$this -> assign("vo", $result['info']);
				$this -> display();
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		} else {

			$id = I('post.id', 0);
			$entity = array('name' => I('post.name', ''), 'description' => I('post.description', ' '), );

			$result = apiCall('Admin/WxuserGroup/saveByID', array($id, $entity));
			if ($result['status'] === false) {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			} else {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			}

		}
	}

	public function powerEdit() {
		if (IS_GET) {
			$map = array('wxuser_group_id' => I('get.groupid', 0));
			$result = apiCall('Admin/GroupAccess/getInfo', array($map));
			if ($result['status']) {
				$this -> assign("access", $result['info']);
				$this -> display();
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

		} elseif (IS_POST) {
			
			$id = I('post.id',0);
			
			$entity = array(
				'alloweddistribution'=>I('post.alloweddistribution',0),
				'allowedcomment'=>I('post.allowedcomment',0),
			);
			
			$result = apiCall('Admin/GroupAccess/saveByID', array($id,$entity));
			
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

		}

	}

	public function delete($redirect_url = false) {
		if (IS_GET) {
			$id = I('get.id', 0);
			$result = apiCall("Admin/Wxuser/countWxusers", array($id));

			if ($result['status']) {
				if ($result['info'] > 0) {
					$this -> error("请先去除关联此会员组的会员！");
				}
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

			$result = apiCall('Admin/WxuserGroup/delWithAccess', array($id));
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		} elseif (IS_POST) {

		}
	}
	/**
	 * 所属用户组的会员管理
	 * 
	 */
	public function subMember(){
		$map = array();
		$groupid = I('groupid', -1);
		
		$map['status'] = array('egt', 0);
		if ($groupid != -1) {
			$map['group_id'] = $groupid;
		}
		$memberMap = array();

		//用户组
		$result = apiCall("Admin/WxuserGroup/queryNoPaging", array($map));
		if ($result['status']) {
			if ($groupid === -1) {
				$groupid = $result['info'][0]['id'];
			}

			$this -> assign("groupid", $groupid);
			$this -> assign("groups", $result['info']);

			$memberMap['status'] = array('egt', 0);
			$memberMap['groupid'] = $groupid;
			//查询用户信息
			//TODO:
			$result = apiCall("Admin/Wxuser/query", array($memberMap, array('curpage' => I('p', 0), 'size' => 10)));
			if ($result['status']) {
				$this -> assign("show", $result['info']['show']);
				$this -> assign("list", $result['info']['list']);
			}
			$this -> display();
		} else {
			$this -> error($result['info']);
		}
	}
	
	

}
