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
 * CREATE TABLE IF NOT EXISTS `boye_2015cjfx`.`cjfx_wxaccount` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `wxname` VARCHAR(60) NOT NULL COMMENT '微信昵称',
  `appid` VARCHAR(45) NOT NULL COMMENT 'appid',
  `appsecreat` VARCHAR(128) NOT NULL COMMENT 'appsecret',
  `encodingAESKey` CHAR(43) NOT NULL COMMENT '微信消息加密密钥',
  `token` CHAR(32) NOT NULL COMMENT 'token,唯一，非微信接口配置中的token',
  `uid` INT NOT NULL COMMENT '用户ID-用户登录账号ID',
  `weixin` CHAR(30) NOT NULL COMMENT '微信号',
  `headerpic` CHAR(255) NOT NULL COMMENT '头像地址',
  `createtime` INT NOT NULL COMMENT '创建时间',
  `updatetime` INT NOT NULL COMMENT '更新时间',
  `tplid` INT NOT NULL COMMENT '模板id',
  `wxuid` VARCHAR(32) NOT NULL COMMENT '微信原始号',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `token_UNIQUE` (`token` ASC))
ENGINE = MyISAM
 * 
 * 
  `tplid` INT NOT NULL COMMENT '模板id',
 */

class WxaccountModel extends Model{
	//自动验证
	protected $_validate = array(
		array('wxname','require','微信昵称必须！'),
		array('appid','require','appid必须！'),
		array('appsecret','require','appsecret必须！'),
		array('token','require','token必须！'),
		array('weixin','require','微信号必须！'),
		array('headerpic','require','头像地址必须！'),
		array('wxuid','require','微信号必须！'),
		array('qrcode','require','公众号二维码图片地址必须！'),
		
	);
	
	//自动完成
	protected $_auto = array(
		array('tplid', 0, self::MODEL_INSERT), 
		array('uid', 0, self::MODEL_INSERT), 
		array('updatetime', 'time', self::MODEL_BOTH,'function'), 
		array('createtime', NOW_TIME, self::MODEL_INSERT), 
		array('status', '1', self::MODEL_INSERT), 
	);
	
}
