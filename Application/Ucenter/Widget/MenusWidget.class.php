<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Widget;
use Ucenter\Controller\UcenterController;

/*
 * 管理导航菜单
 * */
class MenusWidget extends UcenterController {
	
	/**
	 * 左
	 */
	public function left() {
		if (session("?activemenuid")) {
			$list = false;
			//缓存，activesubmenuid
			$list = session('left_menu'. session('activemenuid'));
			
			if ($list === false || is_null($list)) {//未缓存、或过期
				
				$map = array('pid' => session('activemenuid'));
				$develop_mode = C('DEVELOP_MODE');
				if (is_null($develop_mode) || $develop_mode == 0) {
					$map['is_dev'] = 0;
				}
				$result = apiCall('Ucenter/Menu/queryShowingMenu', array($map, ' sort desc '));
				if ($result['status']) {
					$list = $result['info'];
					$hasSubmenuID = false;
					//TODO: 为了速度 可以考虑把 二级菜单，三级菜单一起查询出来，再来组合成需要的数据结构,而不必如下，进行多次查询
					$current_menus = session("CURRENT_USER_".UID."_MENU");	
							
					foreach ($list as &$vo) {
						
						//不在菜单id中且非超级管理员	
						if(strpos($current_menus, $vo['id'].',') === false  && !IS_ROOT){							
							$vo['dynamic_hide'] = true;
						}
						$map['pid'] = $vo['id'];
						$result = apiCall('Ucenter/Menu/queryShowingMenu', array($map, ' sort desc '));
						
						if ($result['status']) {
							$vo['children'] = $result['info'];
							if (!$hasSubmenuID && !empty($vo['children']) && count($vo['children']) > 0) {
								$hasSubmenuID = true;
							}
						}
					}

					//					if (!defined('APP_DEBUG') || !APP_DEBUG) {
					//处于非开发模式下，则缓存，根据activemenuid
					session('left_menu'. session('activemenuid'), $list);
					//					}
					$this -> assign('left_menu', $list);
				} else {
					echo $result['info'];
				}
			} else {
				$this -> assign('left_menu', $list);
			}
			echo $this -> fetch("Widget:left");
		} else {
			//TODO:记录日志
			echo L('UNKNOWN_ERR');
		}
	}

	/**
	 * 顶部
	 */
	public function topbar() {
		$list = session('topbar_menu');
		if ($list === false || is_null($list)) {
			$develop_mode = C('DEVELOP_MODE');
			$map = array('pid' => 0);
			if (is_null($develop_mode) || $develop_mode == 0) {
				$map['is_dev'] = 0;
			}
			$result = apiCall('Ucenter/Menu/queryShowingMenu', array($map, ' sort desc '));

			if ($result['status']) {
				$list = $result['info'];
				
				$current_menus = session("CURRENT_USER_".UID."_MENU");	
				
				foreach ($list as &$vo) {
					//不在菜单id中且非超级管理员					
					if(strpos($current_menus, $vo['id'].',') === false && !IS_ROOT){
						//动态隐藏无权限的菜单												
						$vo['dynamic_hide'] = true;
					}
				}
				session('topbar_menu', $list);
			} else {
				echo $result['info'];
				return;
			}
		}
		if (!is_null($list) && count($list) > 0 && !session('?activemenuid') && I('get.activemenuid', 0) === 0) {
			session('activemenuid', $list[0]['id']);
		}
		$this -> assign('topbar_menu', $list);
		echo $this -> fetch("Widget:topbar");
	}
	/**
	 * 面包屑导航
	 */
	public function breadcrumb() {

		$breadcrumb = array();
		//一级导航
		//TODO: 如果为了链接考虑，可以转为Cookie来存取
		if (session("?activemenuid")) {
			$map = array('id' => session('activemenuid'));
			$result = apiCall('Ucenter/Menu/getInfo', array($map));
			if ($result['status']) {
				array_push($breadcrumb, array('title' => $result['info']['title'], 'url' => getURL($result['info']['url'])));
			} else {
				LogRecord($result['info'], '[FILE]' . __FILE__ . ' [LINE]' . __LINE__);
			}
		}
		//二级导航
		//TODO: 如果为了链接考虑，可以转为Cookie来存取
		if (session("?activesubmenuid")) {
			$map = array('id' => session('activesubmenuid'));
			$result = apiCall('Ucenter/Menu/getInfo', array($map));
			if ($result['status']) {
				array_push($breadcrumb, array('title' => $result['info']['title'], 'url' => getURL($result['info']['url'])));
			} else {
				LogRecord($result['info'], '[FILE]' . __FILE__ . ' [LINE]' . __LINE__);
			}
		}
		//		if(ACTION_NAME != 'index'){
		//			array_push($breadcrumb,array('title'=>L('BREADCRUMB_'.strtoupper(ACTION_NAME)),'url'=>U(MODULE_NAME."/".CONTROLLER_NAME."/index")));
		//		}else{
		//			array_push($breadcrumb,array('title'=>L('BREADCRUMB_'.strtoupper(ACTION_NAME)),'url'=>'#'));
		//		}
		$this -> assign('breadcrumb', $breadcrumb);
		echo $this -> fetch("Widget:breadcrumb");
	}

}
