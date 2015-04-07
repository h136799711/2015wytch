<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Ucenter\Model;
use Think\Model;

/**
 * 基本用户成员信息表
 */
class MemberModel extends Model {
	
	/**
	 * 前缀
	 */
	protected $tablePrefix = "common_";
	
	/* 
	 * 用户模型自动完成 
	 * 
	 * */
	protected $_auto = array(
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
		array('last_login_time', NOW_TIME, self::MODEL_INSERT),
		array('last_login_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
		array('update_time', NOW_TIME),
		array('status', '1', self::MODEL_INSERT),
	);
	
	public function queryByGroup($map,$page){
		$l_table = $this->tablePrefix.'member';
		$r_table = $this->tablePrefix.'auth_group_access';
		$fields = "m.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status";
		$groupid = $map['group_id'];
		$map = array('a.group_id'=>$groupid,'m.status'=>array('egt',0));
		$order = 'm.uid asc';
		$model    = M()->table( $l_table.' m' )->join ( $r_table.' a ON m.uid=a.uid' );
		$list = $model -> field($fields) -> where($map) -> order($order) -> page($page['curpage'] . ',' . $page['size']) -> select();
		if ($list === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}
		
		$count = M()->query('SELECT count(m.uid) FROM common_member m INNER JOIN common_auth_group_access a ON m.uid=a.uid WHERE a.group_id = '.$groupid.' AND m.status >= 0 ORDER BY m.uid asc LIMIT '.$page['curpage'].','.$page['size']);
		$count = $count[0]['count(m.uid)'];

		// 查询满足要求的总记录数
		$Page = new \Think\Page($count, $page['size']);
		
		// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page -> show();

		
//		$result = M()->query('select m.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status from '.$this->tablePrefix.'_member as m right join '.$this->tablePrefix.'_auth_group_access as a on a.uid = m.uid where a.group_id = '.$map['group_id']);
		
		return array("show" => $show, "list" => $list);
	}
	
}