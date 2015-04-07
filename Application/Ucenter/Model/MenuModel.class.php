<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Ucenter\Model;
use Think\Model;

class MenuModel extends Model{
	/**
	 * 前缀
	 */
	protected $tablePrefix = "common_";
	
	protected $_validate = array(
		array('title','require','{%M_MENU_TITLE_REQUIRED}',0,self::MODEL_BOTH),
		array('sort','number','{%M_MENU_SORT_NUMBER}',0,self::MODEL_BOTH),		
     	array('tip',"0,255", '{%M_MENU_TIP_EXCEED_CHARS}',2,'length'), // 当值不为空的时候判断是否在一个范围内
   	);
	protected $insertFields = 'title,sort,tip,url,pid,hide,is_dev,status';
	protected $updateFields = 'title,sort,tip,url,pid,hide,is_dev,status';
	
	protected $_auto = array(
		array('status','1')
	);
}
