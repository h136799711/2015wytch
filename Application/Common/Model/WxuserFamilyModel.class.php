<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Common\Model;
use Think\Model;

/**
 * CREATE TABLE IF NOT EXISTS `boye_2015cjfx`.`cjfx_wxuser_family` (
  `wxuserid` INT NOT NULL,
  `parent_1` INT NOT NULL DEFAULT 0 COMMENT '父一级',
  `parent_2` INT NOT NULL DEFAULT 0 COMMENT '父2级',
  `parent_3` INT NOT NULL DEFAULT 0 COMMENT '父3级',
  `parent_4` INT NOT NULL DEFAULT 0 COMMENT '父4级',
  `parent_5` INT NOT NULL DEFAULT 0 COMMENT '父5级',
  `createtime` INT NOT NULL,
  `id` INT NOT NULL AUTO_INCREMENT,
  `wxaccount_id` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
 */
 
class WxuserFamilyModel extends Model{
	protected $_auto = array(
		array('createtime',NOW_TIME,self::MODEL_INSERT),	
		array('updatetime',NOW_TIME,self::MODEL_BOTH),
	);
	
	protected $_validate = array(
		array('openid','require','所属用户OPENID参数缺少'),
		array('wxaccount_id','require','所属公众号ID参数缺少'),
	);
}
