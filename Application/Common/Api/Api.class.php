<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Api;

/**
 * API基类
 */
abstract class Api {

	/**
	 * API调用模型实例
	 * @access  protected
	 * @var object
	 */
	protected $model;

	/**
	 * 构造方法，检测相关配置
	 */
	public function __construct() {
		$this -> _init();
	}

	/**
	 * 抽象方法，用于设置模型实例
	 */
	abstract protected function _init();

	/**
	 * 返回错误结构
	 * @return array('status'=>boolean,'info'=>Object)
	 */
	protected function apiReturnErr($info) {
		return array('status' => false, 'info' => $info);
	}

	/**
	 * 返回成功结构
	 * @return array('status'=>boolean,'info'=>Object)
	 */
	protected function apiReturnSuc($info) {
		return array('status' => true, 'info' => $info);
	}

	/**
	 * 返回结构
	 * @return array('status'=>boolean,'info'=>Object)
	 */
	protected function apiReturn($status, $info) {
		return array('status' => $status, 'info' => $info);
	}

	/**
	 * get model
	 */
	public function getModel() {
		return $this -> model;
	}
	
	/**
	 * 求和统计
	 */
	public function sum($map,$field){
		
		$result = $this -> model -> where($map) -> sum($field);
		
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		} else {
			return $this -> apiReturnSuc($result);
		}
	}
	
	/**
	 * 数量统计
	 */
	public function count($map, $field = false) {
		if ($field === false) {
			$result = $this -> model -> where($map) -> count();
		} else {
			$result = $this -> model -> where($map) -> count($field);
		}
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		} else {
			return $this -> apiReturnSuc($result);
		}
	}

	/**
	 * 已受理
	 * 必须有status字段 ，5为受理状态
	 */
	public function waitCheck($map) {
		return $this -> save($map, array('status' => 5));
	}

	/**
	 * 待审核
	 * 必须有status字段 ，2为待审核状态
	 */
	public function needCheck($map) {
		return $this -> save($map, array('status' => 2));
	}

	/**
	 * 审核不通过
	 * 必须有status字段 ，4为审核不通过状态
	 */
	public function notpass($map) {
		return $this -> save($map, array('status' => 4));
	}

	/**
	 * 审核通过
	 * 必须有status字段 ，3 为审核通过状态
	 */
	public function pass($map) {
		return $this -> save($map, array('status' => 3));
	}

	/**
	 * 禁用
	 * 必须有status字段 ，0 为禁用状态
	 */
	public function disable($map) {
		return $this -> save($map, array('status' => 0));
	}

	/**
	 * 启用
	 * 必须有status字段，1 为启用状态
	 */
	public function enable($map) {
		return $this -> save($map, array('status' => 1));
	}

	/**
	 * 假删除
	 * 必须有status字段，且 －1 为删除状态
	 */
	public function pretendDelete($map) {
		return $this -> save($map, array('status' => -1));
	}

	/**
	 * 根据id保存数据
	 */
	public function saveByID($ID, $entity) {
		unset($entity['id']);

		return $this -> save(array('id' => $ID), $entity);
	}

	/**
	 * 数字类型字段有效
	 * @param $map 条件
	 * @param $field 更改字段
	 * @param $cnt 增加的值
	 * @return 返回影响记录数 或 错误信息
	 * */
	public function setInc($map, $field, $cnt = 1) {
		$result = $this -> model -> where($map) -> setInc($field, $cnt);
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		} else {
			return $this -> apiReturnSuc($result);
		}
	}

	/**
	 * 数字类型字段有效
	 * @param $map 条件
	 * @param $field 更改字段
	 * @param $cnt 减少的值
	 * @return 返回影响记录数 或 错误信息
	 * */
	public function setDec($map, $field, $cnt = -1) {
		$result = $this -> model -> where($map) -> setDec($field, $cnt);
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		} else {
			return $this -> apiReturnSuc($result);
		}
	}

	/**
	 * 保存
	 * @return status|boolean , info 错误信息或更新条数
	 */
	public function save($map, $entity) {

		$result = $this -> model -> create($entity, 2);
		if($result === false){
			$error = $this -> model -> getError();
			return $this -> apiReturnErr($error);
		}
		
		$result = $this -> model -> where($map) -> save();
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		} else {
			return $this -> apiReturnSuc($result);
		}
	}

	/**
	 * 获取数据find
	 */
	public function getInfo($map,$order=false) {
		if($order === false){
			$result = $this -> model -> where($map) -> find();
		}else{
			$result = $this->model->where($map)->order($order)->find();
		}
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		} else {
			return $this -> apiReturnSuc($result);
		}
	}

	/**
	 * 删除
	 * @map 条件
	 * @result array('status'=>'false|true',$info=>'错误信息|删除数据数')
	 */
	public function delete($map) {
		//		dump($map);
		//		exit();
		$result = $this -> model -> where($map) -> delete();
		if ($result === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		} else {
			return $this -> apiReturnSuc($result);
		}
	}

	/**
	 * add 添加
	 * @return boolean |
	 */
	public function add($entity) {
//		dump($entity);
		$result = $this -> model -> create($entity, 1);
		if ($result === false) {
			$error = $this -> model -> getError();
			return $this -> apiReturnErr($error);
		} else {
			$result = $this -> model -> add();
			if ($result === false) {
				return $this -> apiReturnErr($this -> model -> getDbError());
			}
			return $this -> apiReturnSuc($result);
		}
	}
	
	/**
	 * 批量插入
	 * @param $list 数组
	 */	
	public function addAll($list){
		
		$result = $this->model->addAll($list);
		if($result === FALSE){
			return $this->apiReturnErr($this->model->getError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}
	/**
	 * query 不分页
	 * @param $map 查询条件
	 * @param $order 排序条件
	 * @param $fields 只获取指定字段
	 */
	public function queryNoPaging($map = null, $order = false, $fields = false) {
		$query = $this->model;
		if(!is_null($map)){
			$query = $query->where($map);
		}
		if(!($order === false)){
			$query = $query->order($order);
		}
		if(!($fields === false)){
			$query = $query->field($fields);
		}
		$list = $query -> select();
		//旧
//		if (is_null($map)) {
//			if ($order === false) {
//				if ($fields === false) {
//					$list = $this -> model -> select();
//				} else {
//					$list = $this -> model -> field($fields) -> select();
//				}
//			} else {
//				if ($fields === false) {
//					$list = $this -> model -> order($order) -> select();
//				} else {
//					$list = $this -> model -> field($fields) -> order($order) -> select();
//				}
//			}
//		} elseif ($order === false) {
//			if ($fields === false) {
//				$list = $this -> model -> where($map) -> select();
//			} else {
//				$list = $this -> model -> field($fields) -> where($map) -> select();
//			}
//		} else {
//			if ($fields === false) {
//				$list = $this -> model -> where($map) -> order($order) -> select();
//			} else {
//				$list = $this -> model -> field($fields) -> where($map) -> order($order) -> select();
//			}
//
//		}

		if ($list === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}

		return $this -> apiReturnSuc($list);

	}

	/**
	 * query
	 * @param map 查询条件
	 * @param page 分页参数
	 * @param order 排序参数
	 * @param params 点击分页时带参数
	 */
	public function query($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false, $fields = false) {
		$query = $this->model;
		if(!is_null($map)){
			$query = $query->where($map);
		}
		if(!($order === false)){
			$query = $query->order($order);
		}
		if(!($fields === false)){
			$query = $query->field($fields);
		}
		$list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();
		

		if ($list === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}

		$count = $this -> model -> where($map) -> count();
		// 查询满足要求的总记录数
		$Page = new \Think\Page($count, $page['size']);

		//分页跳转的时候保证查询条件
		if ($params !== false) {
			foreach ($params as $key => $val) {
				$Page -> parameter[$key] = urlencode($val);
			}
		}

		// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page -> show();

		return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
	}

}
