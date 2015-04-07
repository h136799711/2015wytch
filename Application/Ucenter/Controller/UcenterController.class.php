<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Controller;
use Common\Controller\CheckLoginController;

class UcenterController extends CheckLoginController {

	protected $NOT_SUPPORT_METHOD = "不支持的请求方法！";

	protected function _initialize() {
		parent::_initialize();
		$this -> assign("user", session("global_user"));
		//当前一级导航激活menu
		if (I('get.activemenuid', 0) !== 0) {
			session('activemenuid', I('get.activemenuid'));
			session('activesubmenuid', 0);
		}
		//当前三级导航
		if (I('get.activesubmenuid', 0) !== 0) {
			session('activesubmenuid', I('get.activesubmenuid'));
		}
		//获取配置
		$this -> getConfig();
		//对页面一些配置赋值
		$this -> assignPageVars();
		// 是否是超级管理员
		define('IS_ROOT', is_administrator());
		// 当前用户的菜单
		$this -> get_current_usermenu();
		//检测IP是否受限制
		$this -> checkAllowIP();

		//定义版本
		if (defined("APP_DEBUG") && APP_DEBUG) {
			define("APP_VERSION", time());
		} else {
			define("APP_VERSION", C('APP_VERSION'));
		}
		//权限检测
//		if ($this -> checkAuthority() === false) {
//			$this -> error(L('ERR_NO_PERMISSION'));
//		}
	}

	//===================权限相关START=======================

	/**
	 * 获取当前用户的菜单列表ID
	 */
	private function get_current_usermenu() {

		if (session("?CURRENT_USER_" . UID . "_MENU")) {
			return session("CURRENT_USER_" . UID . "_MENU");
		}
		
		$map = array('uid' => UID);
		
		$result = apiCall('Ucenter/AuthGroupAccess/queryNoPaging', array($map));
		
		$menulist = "";
		if ($result['status']) {
			$group_ids = '';
			foreach ($result['info'] as $groupaccess) {
				$group_ids .= $groupaccess['group_id'] . ',';
			}
			unset($map['uid']);
			if (!empty($group_ids)) {
				$map = array('id' => array('in', rtrim($group_ids, ",")));
				$result = apiCall('Ucenter/AuthGroup/queryNoPaging', array($map));

				if ($result['status'] && is_array($result['info'])) {
					//TODO:未测试过多角色的情况下,menulist字段必须,号结尾
					foreach ($result['info'] as $group) {
						$menulist .= $group['menulist'];
					}

				}
			} else {
					
			}
		}

		session("CURRENT_USER_" . UID . "_MENU", $menulist);
		return $menulist;
	}

	public function checkAuthority() {
		//是系统管理员则都可以访问
		if (IS_ROOT) {
			return true;
		}

		$access = $this -> accessControl();
		if (false === $access) {
			$this -> error('403:禁止访问');
		} elseif (null === $access) {
			//检测访问权限
			$rule = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME);
			if (!$this -> checkRule($rule, array('in', '1,2'))) {
				$this -> error('未授权访问!');
			} else {
				// 检测分类及内容有关的各项动态权限
				$dynamic = $this -> checkDynamic();
				if (false === $dynamic) {
					$this -> error('未授权访问!');
				}
			}
		}
		//TODO:检测权限
		return true;
	}

	/**
	 * 权限检测
	 * @param string  $rule    检测的规则
	 * @param string  $mode    check模式
	 * @return boolean
	 */
	private function checkRule($rule, $mode = 'url') {
		static $Auth = null;
		if (!$Auth) {
			$Auth = new \Think\Auth();
		}

		if (!$Auth -> check($rule, UID, 2, $mode)) {
			return false;
		}

		return true;
	}

	/**
	 * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
	 *
	 * @return boolean|null  返回值必须使用 `===` 进行判断
	 *
	 *   返回 **false**, 不允许任何人访问(超管除外)
	 *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
	 *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
	 *
	 */
	private function accessControl() {
		$allow = C('ALLOW_VISIT');
		$deny = C('DENY_VISIT');
		$check = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
		if (!empty($deny) && in_array_case($check, $deny)) {
			return false;
			//非超管禁止访问deny中的方法
		}
		if (!empty($allow) && in_array_case($check, $allow)) {
			return true;
		}
		return null;
		//需要检测节点权限
	}

	private function checkDynamic() {
		return true;
	}

	//===================权限相关END=======================

	/**
	 * 检测IP是否在运行访问的IP里
	 */
	public function checkAllowIP() {
		$allowIP = C('ADMIN_ALLOW_IP');
		if (!IS_ROOT && $allowIP) {
			// 检查IP地址访问
			if (!in_array(get_client_ip(), explode(',', $allowIP))) {
				$this -> error('403:禁止访问');
			}
		}
	}

	//页面上变量赋值
	public function assignPageVars() {
		$seo = array('title' => C('WEBSITE_TITLE'), 'keywords' => C('WEBSITE_KEYWORDS'), 'description' => C('WEBSITE_DESCRIPTION'));
		$cfg = array('owner' => C('WEBSITE_OWNER'), 'statisticalcode' => C('WEBSITE_STATISTICAL_CODE'), 'theme' => getSkin(C('DEFAULT_SKIN')), );
		//若是晚上 则固定为darkly样式
		if (isNight()) {
			$cfg['theme'] = "darkly";
		}
		//
		$this -> assignVars($seo, $cfg);
	}

	/**
	 * 从数据库中取得配置信息
	 */
	protected function getConfig() {
		$config = S('config_' . session_id() . '_' . session("uid"));
		if (APP_DEBUG === true) {
			//调试模式下，不缓存配置
			$config = false;
		}
		if ($config === false) {
			$map = array();
			$fields = 'type,name,value';
			$result = apiCall('Ucenter/Config/queryNoPaging', array($map, false, $fields));
			if ($result['status']) {
				$config = array();
				if (is_array($result['info'])) {
					foreach ($result['info'] as $value) {
						$config[$value['name']] = $this -> parse($value['type'], $value['value']);
					}
				}
				//缓存配置300秒
				S("config_" . session_id() . '_' . session("uid"), $config, 300);
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		}
		C($config);
	}

	/**
	 * 根据配置类型解析配置
	 * @param  integer $type  配置类型
	 * @param  string  $value 配置值
	 */
	private static function parse($type, $value) {
		switch ($type) {
			case 3 :
				//解析数组
				$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
				if (strpos($value, ':')) {
					$value = array();
					foreach ($array as $val) {
						list($k, $v) = explode(':', $val);
						$value[$k] = $v;
					}
				} else {
					$value = $array;
				}
				break;
		}
		return $value;
	}

	//===========================重复代码

	/**
	 * 假删除
	 */
	protected function pretendDelete($primaryKey = 'id') {
		$id = I($primaryKey, -1);
		$ids = I($primaryKey . 's', -1);
		if ($ids === -1 && $id === -1) {
			$this -> error("缺少参数！");
		}
		if ($ids === -1) {
			$map = array($primaryKey => $id);
		} else {
			$map = array();
			$ids = implode(',', $ids);
			$map = array($primaryKey => array('in', $ids));
		}

		$result = apiCall("Ucenter/" . CONTROLLER_NAME . '/pretendDelete', array($map));

		if ($result['status']) {

			$this -> success("删除成功！", U('Ucenter/' . CONTROLLER_NAME . '/index'));

		} else {
			$this -> error($result['info']);
		}
	}

	/**
	 * 启用/批量启用
	 * id,ids
	 */
	public function enable($primaryKey = 'id') {
		$id = I($primaryKey, -1);
		$ids = I($primaryKey . 's', -1);
		if ($ids === -1 && $id === -1) {
			$this -> error("缺少参数！");
		}
		if ($ids === -1) {
			$map = array($primaryKey => $id);
		} else {
			$map = array();
			$ids = implode(',', $ids);
			$map = array($primaryKey => array('in', $ids));
		}

		$result = apiCall("Ucenter/" . CONTROLLER_NAME . '/enable', array($map));

		if ($result['status']) {
			if (IS_AJAX) {
				$this -> success("启用成功！");
			} else {
				$this -> success("启用成功！", U('Ucenter/' . CONTROLLER_NAME . '/index'));
			}
		} else {
			$this -> error($result['info']);
		}
	}

	/**
	 * 禁用/批量禁用
	 * id,ids
	 */
	public function disable($primaryKey = 'id') {
		$id = I($primaryKey, -1);
		$ids = I($primaryKey . 's', -1);
		if ($ids === -1 && $id === -1) {
			$this -> error("缺少参数！");
		}
		if ($ids === -1) {
			$map = array($primaryKey => $id);
		} else {
			$map = array();

			$ids = implode(',', $ids);
			$map = array($primaryKey => array('in', $ids));
		}

		$result = apiCall("Ucenter/" . CONTROLLER_NAME . '/disable', array($map));

		if ($result['status']) {
			if (IS_AJAX) {
				$this -> success("禁用成功！");
			} else {
				$this -> success("禁用成功！", U('Ucenter/' . CONTROLLER_NAME . '/index'));
			}
		} else {
			$this -> error($result['info']);
		}
	}

	/**
	 * 分页查询结果处理
	 */
	public function queryResult($result) {
		if ($result['status']) {
			$this -> assign("show", $result['info']['show']);
			$this -> assign("list", $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	//===========================通用CRUD操作方法
	/**
	 * 增加菜单
	 * GET:显示
	 * @param $success_url 添加成功后跳转url
	 */
	protected function add($entity, $success_url = false) {
		if (IS_GET) {
			$this -> display();
		} else {
			if ($success_url === false) {
				$success_url = U('Ucenter/' . CONTROLLER_NAME . '/index');
			}
			$result = apiCall('Ucenter/' . CONTROLLER_NAME . '/add', array($entity));
			if ($result['status'] === false) {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			} else {
				$this -> success(L('RESULT_SUCCESS'), $success_url);
			}

		}
	}

	/**
	 * 查看
	 */
	public function view() {
		$map = array('id' => I('get.id'));
		$result = apiCall('Ucenter/' . CONTROLLER_NAME . '/getInfo', array($map));
		if ($result['status'] === false) {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error(L('C_GET_NULLDATA'));
		} else {
			$this -> assign("entity", $result['info']);
			$this -> display();
		}
	}

	/**
	 * 编辑页面展示
	 */
	public function edit() {
		if (IS_GET) {
			$map = array('id' => I('get.id'));
			$result = apiCall('Ucenter/' . CONTROLLER_NAME . '/getInfo', array($map));
			if ($result['status'] === false) {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error(L('C_GET_NULLDATA'));
			} else {
				$this -> assign("entity", $result['info']);
				$this -> display();
			}
		}
	}

	/**
	 * 更新保存，根据主键默认id
	 * 示列url:
	 * /Ucenter/Menu/save/id/33
	 * id必须以get方式传入
	 */
	public function save($primarykey = 'id', $entity = null, $redirect_url = false) {
		if (IS_POST) {
			if ($redirect_url === false) {
				$redirect_url = U('Ucenter/' . CONTROLLER_NAME . '/index');
			}

			if (is_null($entity)) {
				$entity = I('post.');
			}

			$result = apiCall('Ucenter/' . CONTROLLER_NAME . '/saveByID', array(I('get.' . $primarykey, 0), $entity));
			if ($result['status'] === false) {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			} else {
				$this -> success(L('RESULT_SUCCESS'), $redirect_url);
			}
		} else {
			$this -> error("不支持get方式save");
		}
	}

	/**
	 * 删除
	 * @param $success_url 删除成功后跳转
	 */
	public function delete($success_url = false) {
		if ($success_url === false) {
			$success_url = U('Ucenter/' . CONTROLLER_NAME . '/index');
		}
		$map = array('id' => I('id', -1));

		$result = apiCall('Ucenter/' . CONTROLLER_NAME . '/delete', array($map));

		if ($result['status'] === false) {
			LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		} else {
			$this -> success(L('RESULT_SUCCESS'), $success_url);
		}
	}

	/**
	 * 批量删除
	 * @param $success_url 删除成功后跳转
	 */
	public function bulkDelete($success_url = false) {
		if ($success_url === false) {
			$success_url = U('Ucenter/' . CONTROLLER_NAME . '/index');
		}
		$ids = I('ids', -1);
		if ($ids === -1) {
			$this -> error(L('ERR_PARAMETERS'));
		}
		$ids = implode(',', $ids);
		$map = array('id' => array('in', $ids));

		$result = apiCall('Ucenter/' . CONTROLLER_NAME . '/delete', array($map));

		if ($result['status'] === false) {
			LogRecord('[INFO]' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		} else {
			$this -> success(L('RESULT_SUCCESS'), $success_url);
		}
	}

}
