<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Controller;

class QrcodeController extends Controller{
	
	public function index(){
		//TODO: 生成二维码
		vendor("Org.PhpQrcode.QrcodeHelper");
		$qrcode = new QrcodeHelper();
		
		dump($qrcode);
		
	}
}
