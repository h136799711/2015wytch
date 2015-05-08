<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Org\PhpQrcode;

require_once ('./phpqrcode.php');

class QrcodeHelper {
	
	protected $qrcode;
	
	function __construct(){
		$this->qrcode = new Qrcode;
	}
	
	public function  toPng($str){
		return QRcode::png($str); 
	}
	
	
}
