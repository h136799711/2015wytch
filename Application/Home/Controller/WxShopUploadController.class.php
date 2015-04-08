<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

class WxShopUploadController extends Controller{
	private $wxshopapi;
	
	protected function _initialize(){
		$appid = "wx58aea38c0796394d";
		$appsecret = "3e1404c970566df55d7314ecfe9ff437";
		$this->wxshopapi = new \Common\Api\WxShopApi($appid,$appsecret);
		dump("initialize");
	}
	
	
	public function index(){
		$this->display();
	}
	
	public function upload(){
		$appid = "wx58aea38c0796394d";
		$appsecret = "3e1404c970566df55d7314ecfe9ff437";
		$wxshopapi = new \Common\Api\WxShopApi($appid,$appsecret);
		if(IS_POST){
//			dump($_FILES);
			$tmp_name = $_FILES['file']['tmp_name'];
//			$filename = "./Public/Shop/imgs/btn/weixin-pay.jpg";
			$result = $wxshopapi->uploadImg("weixin-pay.jpg",$tmp_name);
			dump($result);
		}else{
			$this->assign("token",$wxshopapi->getAccessToken());
			$this->display();
		}
	}
	
	public function getSKU(){
		
		$appid = "wx58aea38c0796394d";
		$appsecret = "3e1404c970566df55d7314ecfe9ff437";
		$wxshopapi = new \Common\Api\WxShopApi($appid,$appsecret);
		
		if(IS_POST){
			$cate_id = I('cate_id',1);
			$result = $wxshopapi->getSKU($cate_id);
			dump($result);
		}else{
			$this->display();
		}
	}
	
	public function category(){
		
		$appid = "wx58aea38c0796394d";
		$appsecret = "3e1404c970566df55d7314ecfe9ff437";
		$wxshopapi = new \Common\Api\WxShopApi($appid,$appsecret);
		
		if(IS_POST){
			$cate_id = I('cate_id',1);
			$result = $wxshopapi->category($cate_id);
			dump($result);
		}else{
			$this->display();
		}
		
	}
	//分组测试＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
	
	public function groupModProduct(){
		$groupid = 200781536;
		$groupname="分组名称修改测试";
		$product_list = array(array("product_id"=>"pqMIVt1isZB4WKMzEmvE72w1Z94A","mod_action"=>1));
		
		$result = $this->wxshopapi->groupModProduct($groupid,$product_list);
		dump($result);
		
	}
	
	public function groupMod(){
		$groupid = 200781536;
		$groupname="分组名称修改测试";
		
		$result = $this->wxshopapi->groupModify($groupid,$groupname);
		dump($result);
		
	}
	
	public function groupDel(){
		$group_id = 205634813;
		$result = $this->wxshopapi->groupDel($group_id);
		dump($result);
	}
	
	public function groupAdd(){
		$group_name = "分组测试";
		$product_list = array();
		$result = $this->wxshopapi->groupAdd($group_name,$product_list);
		dump($result);
	}
	
	public function groupByID(){
		$groupid = 200781536;
		$result = $this->wxshopapi->groupGetByID($groupid);
		dump($result);
	}
	
	//分组测试
	public function groupGetAll(){
		
		$appid = "wx58aea38c0796394d";
		$appsecret = "3e1404c970566df55d7314ecfe9ff437";
		$wxshopapi = new \Common\Api\WxShopApi($appid,$appsecret);
		$result = $wxshopapi->groupGetAll();
		dump($result);
		
//		if(IS_POST){
//			$cate_id = I('cate_id',1);
//			$result = $wxshopapi->category($cate_id);
//			dump($result);
//		}else{
//			$this->display();
//		}
	}
	
	
}
