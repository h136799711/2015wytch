<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class WxmenuController extends AdminController {

	public function test() {
		$json = array('button' => array( array('type' => "click")));
		echo json_encode($json);
	}

	public function index() {
		$map = array('wxaccount_id' => getWxAccountID());
		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " sort desc ";
		//
		$result = apiCall('Admin/Wxmenu/query', array($map, $page, $order));
		if ($result['status']) {
			$this -> assign("show", $result['info']['show']);
			$this -> assign("list", $result['info']['list']);
			$this -> display();
		} else {
			LogRecord($result['info'], __FILE__);
			$this -> error($result['info']);
		}
	}

	public function add() {
		if (IS_GET) {
			//获取主菜单 $order = false, $fields
			$map = array('wxaccount_id' => getWxAccountID(), 'pid' => 0);

			$result = apiCall('Admin/Wxmenu/queryNoPaging', array($map, "sort desc", "name,id"));
			if ($result['status']) {
				$this -> assign("mainmenus", $result['info']);
				$this -> display();
			} else {

				LogRecord($result['info'], __FILE__);
				$this -> error($result['info']);
			}

		} elseif (IS_POST) {
			$entity = array('name' => I('post.name', ''), //菜单名称
			'menukey' => I('post.key', ''), 'url' => I('post.url', ''), 'sort' => I('post.sort', '1'), 'wxaccount_id' => getWxAccountID(), 'pid' => I('post.pid', 0), );
			if (!empty($entity['url'])) {
				$entity['type'] = "view";
			} else {
				$entity['type'] = 'click';
			}

			$result = apiCall("Admin/Wxmenu/add", array($entity));
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/Wxmenu/index'));
			} else {
				LogRecord($result['info'], __FILE__);
				$this -> error($result['info']);
			}
		}
	}

	public function edit() {
		if (IS_GET) {

			$id = I('get.id', 0);
			$result = apiCall("Admin/Wxmenu/getInfo", array( array('id' => $id)));

			if ($result['status']) {
				$this -> assign("menuVO", $result['info']);
				$this -> assign("pid", $result['info']['pid']);
			}

			//获取主菜单 $order = false, $fields
			$map = array('wxaccount_id' => getWxAccountID(), 'pid' => 0);

			$result = apiCall('Admin/Wxmenu/queryNoPaging', array($map, "sort desc", "name,id"));
			if ($result['status']) {
				$this -> assign("mainmenus", $result['info']);
				$this -> display();
			} else {

				LogRecord($result['info'], __FILE__);
				$this -> error($result['info']);
			}

		} elseif (IS_POST) {
			$id = I('post.id', 0);
			$entity = array('name' => I('post.name', ''), //菜单名称
			'menukey' => I('post.key', ''), 'url' => I('post.url', ''), 'sort' => I('post.sort', '1'), 'pid' => I('post.pid', 0), );
			if (!empty($entity['url'])) {
				$entity['type'] = "view";
			} else {
				$entity['type'] = 'click';
			}
			$result = apiCall("Admin/Wxmenu/saveByID", array($id, $entity));
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/Wxmenu/index'));
			} else {
				LogRecord($result['info'], __FILE__);
				$this -> error($result['info']);
			}
		}
	}
	
	
	public function deleteMenu() {
		if (IS_POST) {

			$result = apiCall("Admin/Wxaccount/getInfo", array( array('id' => getWxAccountID())));
			if ($result['status']) {
				$appid = $result['info']['appid'];
				$appsecret = $result['info']['appsecret'];
				
				$weixinApi = new \Common\Api\WeixinApi($appid, $appsecret);
				$result = $weixinApi -> deleteMenu();
				if ($result['status']) {
					$this -> success(L("RESULT_SUCCESS"));
				} else {
					LogRecord($result['msg'], __FILE__);
					$this -> error($result['msg']);
				}
				//

			} else {
				LogRecord($result['info'], __FILE__);
				$this -> error($result['info']);
			}
		}
	}

	/**
	 * 发送到微信服务器
	 */
	public function sendToWXServer() {
		if (IS_POST) {
			$map = array('wxaccount_id' => getWxAccountID());
			$result = apiCall('Admin/Wxmenu/queryNoPaging', array($map, "sort desc", "name,id,pid,menukey,url,type"));
			if ($result['status']) {
				$menulist = $result['info'];
				if (count($menulist) > 15) {
					$this -> error("菜单不能超过15个了！");
				}

				$convertML = $this -> convertMenu($menulist);

				$result = apiCall("Admin/Wxaccount/getInfo", array( array('id' => getWxAccountID())));
				if ($result['status']) {
					$appid = $result['info']['appid'];
					$appsecret = $result['info']['appsecret'];

					$weixinApi = new \Common\Api\WeixinApi($appid, $appsecret);
					
					$result = $weixinApi -> createMenu($convertML);
					if ($result['status']) {
						$this -> success(L("RESULT_SUCCESS"));
					} else {
						LogRecord($result['msg'], __FILE__);
						$this -> error($result['msg']);
					}
					//

				} else {
					LogRecord($result['info'], __FILE__);
					$this -> error($result['info']);
				}

			} else {
				LogRecord($result['info'], __FILE__);
				$this -> error($result['info']);
			}
		}
	}

	/**
	 * 转换为可处理的数据
	 */
	private function convertMenu($menulist) {
		$convertMl = array();

		//主菜单
		foreach ($menulist as $key => $menu) {
			if ($menu['pid'] == 0) {
				$convertMl[$menu['id']] = array('name' => $menu['name'], 'type' => $menu['type'], 'url' => $menu['url'], 'key' => $menu['menukey'], '_child' => array());
			}
		}

		//子菜单
		foreach ($menulist as $key => $menu) {
			if ($menu['pid'] > 0 && isset($convertMl[$menu['pid']])) {
				//子菜单
				array_push($convertMl[$menu['pid']]['_child'], array('name' => $menu['name'], 'type' => $menu['type'], 'url' => $menu['url'], 'key' => $menu['menukey'], ));
			}
		}

		return $convertMl;
	}

}
