<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace WeixinShop\Controller;
use Think\Controller;
use WeixinShop\Model\CateModel;

class IndexController extends Controller{
	
	public function index(){
		$model = new CateModel();
		$model->id = 1;
		dump($model);
	}
	
	
}

