<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class ProductController extends AdminController {
	protected function _initialize() {
		parent::_initialize();
	}

	public function index() {

//		$startdatetime = I('startdatetime', date('Y-m-d', time() - 24 * 3600), 'urldecode');
//		$enddatetime = I('enddatetime', date('Y-m-d', time()+24*3600), 'urldecode');

		//分页时带参数get参数
		$params = array(
//			'startdatetime' => $startdatetime, 'enddatetime' => $enddatetime
		);
		
//		$startdatetime = strtotime($startdatetime);
//		$enddatetime = strtotime($enddatetime);
		
//		if ($startdatetime === FALSE || $enddatetime === FALSE) {
//			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
//			$this -> error(L('ERR_DATE_INVALID'));
//		}

		$map = array();
		$productname = I('post.name','');
		if(!empty($productname)){
			$map['name'] = array('like',$productname.'%');
		}
		$map['wxaccountid'] = getWxAccountID();
//		$map['createtime'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');

		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
//		$order = " createtime desc ";
		//
		$result = apiCall('Admin/Product/query', array($map, $page, $order, $params));

		//
		if ($result['status']) {
			$this -> assign('productname', $productname);
//			$this -> assign('startdatetime', $startdatetime);
//			$this -> assign('enddatetime', $enddatetime);
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	public function add() {
		if (IS_GET) {
			$this -> display();
		} elseif (IS_POST) {
			$entity = array('name' => I('post.name', '商品'), 'price' => I('post.price', 0), 'dis_price' => I('post.dis_price', 0), 'stock' => I('post.stock', 0), 'sale_num' => I('post.sale_num', 0), 'thumbnail' => I('post.pictureid', 0), 'wxaccountid' => getWxAccountID(), );
			//			dump($entity);
			parent::addTo($entity);

		}
	}

	public function edit() {
		if (IS_GET) {
			$id = I('get.id', 0);
			//thumbnailurl
			$result = apiCall("Admin/Product/getInfoWithThumbnail", array($id));
			if($result['status']){
				$result['info']['tburl'] = getPictureURL($result['info']['thumbnaillocal'],$result['info']['thumbnailremote']);
				$this->assign("product",$result['info']);
				$this -> display();
			}else{
				$this->error($result['info']);
			}
		} elseif (IS_POST) {
			
			$id = I('post.id', 0);
			$entity = array('name' => I('post.name', '商品'), 
							'price' => I('post.price', 0), 
							'dis_price' => I('post.dis_price', 0), 
							'stock' => I('post.stock', 0), 
							'sale_num' => I('post.sale_num', 0), 
							'thumbnail' => I('post.pictureid', 0));
			
			$result = apiCall("Admin/Product/saveByID", array($id, $entity));
			if ($result['status']) {
				$this->success(L('RESULT_SUCCESS'),U('Admin/Product/index'));
			} else {

				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		}
	}
	public function view(){
		if (IS_GET) {
			$id = I('get.id', 0);
			//thumbnailurl
			$result = apiCall("Admin/Product/getInfoWithThumbnail", array($id));
			if($result['status']){
				$result['info']['tburl'] = getPictureURL($result['info']['thumbnaillocal'],$result['info']['thumbnailremote']);
				$this->assign("product",$result['info']);
				$this -> display();
			}else{
				$this->error($result['info']);
			}
		}
	}
}
