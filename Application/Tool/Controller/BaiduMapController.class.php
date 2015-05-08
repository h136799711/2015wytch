<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Controller;
use Think\Controller;

class BaiduMapController extends Controller{
	
	/**
	 *  returnurl 返回链接
	 *  title 标题
	 */
	public function index(){
		$ak = C('BAIDU_MAP_KEY');
		$title = I('get.title','','urldecode');
		$header = $title;
		$returnURL = I('get.returnurl','javascript:history.back(-1)');
		$lng = I('get.lng',120.337985);//经度
		$lat = I('get.lat',30.314933);//纬度
		$info = array();
		
		$info['title'] = I('get.title','杭州博也网络科技','urldecode');
		$info['text'] = I('get.text','杭州博也网络科技','urldecode');
		
		
		$this->assign("info",$info);
		$this->assign("lat",$lat);
		$this->assign("lng",$lng);
		$this->assign("returnURL",$returnURL);
		$this->assign("header",$header);
		$this->assign("title",$title);
		$this->assign("ak",$ak);
		$this->display();
	}
}
