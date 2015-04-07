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
 * CREATE TABLE IF NOT EXISTS `boye_2015cjfx`.`cjfx_wxmenu` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wxaccount_id` INT NOT NULL,
  `name` CHAR(16) NOT NULL COMMENT '菜单名称',
  `type` VARCHAR(32) NOT NULL COMMENT ,
  `url` VARCHAR(256) NOT NULL COMMENT '跳转到的url，仅type=view时有效',
  `key` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_cjfx_wxmenu_cjfx_wxaccount1_idx` (`wxaccount_id` ASC),
  CONSTRAINT `fk_cjfx_wxmenu_cjfx_wxaccount1`
    FOREIGN KEY (`wxaccount_id`)
    REFERENCES `boye_2015cjfx`.`cjfx_wxaccount` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
 * `wxaccount_id` INT NOT NULL,
  `name` CHAR(16) NOT NULL COMMENT '菜单名称',
  `type` VARCHAR(32) NOT NULL COMMENT ,
  `url` VARCHAR(256) NOT NULL COMMENT '跳转到的url，仅type=view时有效',
  `key` VARCHAR(128) NOT NULL,
 */
class WxmenuModel extends Model{
	
	protected $_auto = array(
		array('createtime',NOW_TIME,self::MODEL_INSERT),
	);
	protected $_validate = array(
		array("name","require","菜单名称必须！"),
//		array("type","require",""),
	);
		
}
