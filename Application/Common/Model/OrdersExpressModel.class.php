<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Common\Model;
use Think\Model;
/**
 * CREATE TABLE IF NOT EXISTS `boye_2015cjfx`.`cjfx_orders_express` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `expresscode` CHAR(16) NOT NULL COMMENT '发货快递公司编码',
  `expressname` VARCHAR(32) NOT NULL COMMENT '发货快递公司名称',
  `expresslist` VARCHAR(64) NOT NULL COMMENT '发货快递单号',
  `note` VARCHAR(126) NOT NULL COMMENT '备注',
  `ordersid` CHAR(32) NOT NULL,
  `wxuserid` INT NOT NULL COMMENT '冗余用户ID，方便查询用户的快递信息。',
 * 订单的快递信息
 */
class OrdersExpressModel extends Model{
	
	protected $_auto = array(
		array('createtime',"time",self::MODEL_INSERT,"function"),
		array('updatetime',"time",self::MODEL_BOTH,"function"),
	);
	
	protected $_validate = array(
//		array("")
	);
	
} 