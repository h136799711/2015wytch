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
 * CREATE TABLE IF NOT EXISTS `boye_2015cjfx`.`cjfx_wxreply_text` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `keyword` VARCHAR(32) NOT NULL COMMENT '关键词',
  `content` TEXT NOT NULL COMMENT '内容',
  `createtime` INT NULL,
  `updatetime` INT NULL,
  `wxaccount_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_wxreply_cjfx_wxaccount1_idx` (`wxaccount_id` ASC),
  CONSTRAINT `fk_wxreply_cjfx_wxaccount1`
    FOREIGN KEY (`wxaccount_id`)
    REFERENCES `boye_2015cjfx`.`cjfx_wxaccount` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
 */
class WxreplyTextModel extends Model{
	protected $_auto = array(
		array('updatetime',NOW_TIME,self::MODEL_BOTH),
		array('createtime',NOW_TIME,self::MODEL_INSERT),
	);
	
	protected $_validate = array(
		array('content',"require","内容必填",self::EXISTS_VALIDATE),
		array('wxaccount_id','require','公众号ID必须',self::EXISTS_VALIDATE),
		array('keyword','require','关键词必填',self::EXISTS_VALIDATE),
	);
	
	
}
