<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Model;

use Think\Model;

 
class OrdersModel extends Model{
	//订单状态
	
//	1、等待审核
//	2、等待到款
//	3、等待预售商品到货
//	4、正在配货
//	5、等待移仓
//	6、正在移仓
//	7、已配货
//	8、已发货
//	9、已送达
//	10.交易成功
//	11.交易失败

	/*
	 * 1、等待审核：
	在此状态下，您的订单预计1个小时内完成审核。
	在此状态下，您可以对订单做如下操作：
	1）修改订单中收货人姓名、联系方式
	2）添加或修改发票信息
	3）取消订单
	注：分包裹订单无法修改订单信息，若您需修改，建议您进行取消订单操作，重新订购过程中进行信息的修改。同时，需提醒您，取消订单可能会有商品库存及价格的变动，请您以最终提交订单时为准。
	2、等待到款：
	此状态说明当当网还未收到您的订单款项，建议您在订单保留期限内及时付款，避免订单因超时未支付被系统取消；若您有多张订单需“网上支付”，您还可进行订单的合并支付。 
	在此状态下，您可以对订单做如下操作：
	1）修改订单中收货人姓名、联系方式
	2）添加或修改发票信息
	3）取消订单
	注：分包裹订单无法修改订单信息，若您需要修改，建议您进行取消订单操作，重新订购过程中进行信息的修改。同时，需提醒您，取消订单可能会有商品库存及价格的变动，请您以最终提交订单时为准。
	3、等待预售商品到货：
	您的订单正在等待预售商品到货，待到货后我们会及时处理您的订单，请您耐心等待。
	在此状态下，您可以对订单做如下操作：
	1）修改订单中收货人姓名、联系方式
	2）添加或修改发票信息
	3）取消订单
	注：分包裹订单无法修改订单信息，若您需要修改，建议您进行取消订单操作，重新订购过程中进行信息的修改。同时，需提醒您，取消订单可能会有商品库存及价格的变动，请您以最终提交订单时为准。
	4、正在配货：
	此状态说明您的订单正在库房配货。
	在此状态下，您可以对订单进行“取消”操作。
	5、等待移仓：
	若您订单中的商品当地仓库不能完全满足，我们将从北京仓库发送这些商品至当地仓库。
	在此状态下，您可以对订单进行“取消”操作。 
	6、正在移仓：
	此状态说明您订单中的商品正在从北京仓库发往当地仓库。
	在此状态下，您可以对订单进行“取消”操作。
	7、已配货：
	此状态说明您的订单已完成配货，正在等待发货。
	8、已发货：
	此状态说明您的订单已从库房发出，正在配送途中，想要了解您订单的送达时间，您可通过订单详情查看预计送达时间及配送详细信息。
	9、已送达：
	此状态说明您已收到商品并在“我的订单”中进行了 “收货反馈”；或若您未进行“收货反馈”操作，系统在发货后的20天默认变为“已送达”；或订单状态是“已发货”，且订单中的物流配送信息是配送成功，此时，系统会默认将您的订单状态显示为“已送达”。
	注：
	1）国内平邮订单，如果您未进行“确认收货”操作，系统会在发货25天后默认您收到商品，订单状态显示为“已送达”；
	2）针对海外订单，如果您未进行“确认收货”操作，系统会在发货60天后默认您收到商品，订单状态显示为“已送达”；
	3）若您误点击“收货反馈”，请不要担心，我们仍会正常为您配送订单。预计送达时间您可参看“配送时间及运费”；
	4）若您已收到货，此时，您可以对订单中的商品进行评价并获得相应积分，详情请参看“我的评价”。
	 * */
	// 10.交易成功
	// 若您的订单状态为“已送达”，则此状态后的15天内没有发生退货，系统将默认变为“交易成功”。
	// 
	// 11.交易未成功
	// 12、取消：
	// 若您订单中所订的商品缺货，或您的订单过了等款的订单保留期限，您的订单将被系统取消；或若您将订单进行取消操作，也将显示“取消”状态。
	/**
	 * 待确认，
	 */
	const ORDER_TOBE_CONFIRMED = 2;
	/**
	 * 待发货
	 */
	const ORDER_TOBE_SHIPPED = 3;
	/**
	 * 已发货
	 */
	const ORDER_SHIPPED = 4;
	/**
	 * 已收货
	 */
	const ORDER_RECEIPT_OF_GOODS = 5;
	/**
	 * 已退货
	 */
	const ORDER_RETURNED = 6;
	/**
	 * 已完成
	 */
	const ORDER_COMPLETED = 7;
	/**
	 * 取消或交易关闭
	 */
	const ORDER_CANCEL = 8;
	
	
	//订单支付状态
	/**
	 * 待支付
	 */
	const ORDER_TOBE_PAID = 0;
	/**
	 * 货到付款
	 */
	const ORDER_CASH_ON_DELIVERY = 3;
	/**
	 * 已支付
	 */
	const ORDER_PAID = 1;
	/**
	 * 已退款
	 */
	const ORDER_REFUND = 2;
	
	protected $_auto = array(
		array('status',1,self::MODEL_INSERT),
		array('pay_status',self::ORDER_TOBE_PAID,self::MODEL_INSERT),
		array('order_status',self::ORDER_TOBE_CONFIRMED,self::MODEL_INSERT),
		array('createtime',NOW_TIME,self::MODEL_INSERT),
		array('updatetime','time',self::MODEL_BOTH,"function"),
	);
}
