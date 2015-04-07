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
 * CREATE TABLE IF NOT EXISTS `boye_2015cjfx`.`cjfx_wxreply_news` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `keyword` VARCHAR(32) NOT NULL COMMENT '关键词',
  `createtime` INT NOT NULL,
  `updatetime` INT NOT NULL,
  `wxaccount_id` INT NOT NULL,
  `description` VARCHAR(256) NOT NULL COMMENT '图文描述',
  `pictureid` int NOT NULL COMMENT '图片表的ID，外键',
  `picurl` VARCHAR(512) NOT NULL COMMENT '图片链接',
  `url` VARCHAR(256) NOT NULL COMMENT '点击图文跳转的链接',
  `title` VARCHAR(64) NOT NULL COMMENT '标题',
  `sort` TINYINT NOT NULL DEFAULT 0 COMMENT '排序，仅在多图文的情况下',
  PRIMARY KEY (`id`),
  INDEX `fk_wxreply_cjfx_wxaccount1_idx` (`wxaccount_id` ASC),
  CONSTRAINT `fk_wxreply_cjfx_wxaccount10`
    FOREIGN KEY (`wxaccount_id`)
    REFERENCES `boye_2015cjfx`.`cjfx_wxaccount` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
 * 
 */
class WxreplyNewsModel extends  Model{
	protected $_auto = array(
		array('sort',0,self::MODEL_INSERT),
		array('updatetime',NOW_TIME,self::MODEL_BOTH),
		array('createtime',NOW_TIME,self::MODEL_INSERT),
		
	);
	
	protected $_validate = array(
		array('wxaccount_id','require','公众号ID必须'),
		array('keyword','require','关键词必填'),
	);
	
}
