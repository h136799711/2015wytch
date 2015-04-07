<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Model;
use Think\Model;
class ProductModel extends Model{
	
	protected $_validate = array(
		array('wxuserid','require','所属用户ID必须'),
		array('stock','chkInt','必须大于等于0',self::EXISTS_VALIDATE,'function'),
	);
	
	protected $_auto = array(
	
		array('detailcontent',"",self::MODEL_INSERT),
		array('rate_num',0,self::MODEL_INSERT),
		array('createtime',NOW_TIME,self::MODEL_INSERT),
		array('updatetime',"time",self::MODEL_BOTH,'function'),
	);
	
	/**
	 * 检测正整数
	 */
	public function chkInt($stock){
		if(is_int($stock) && $stock >= 0){
			return true;
		}
		return false;
	}
}
