<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Api;

use Common\Model\WxreplyNewsModel;

class WxreplyNewsApi extends \Common\Api\Api{
	
	protected function _init(){
		$this->model = new WxreplyNewsModel();	
	}
	
	/**
	 * 查询数据做链接picture表
	 */
	public function queryWithPicture($map,$order){
		$list = $this -> model ->alias("news ")->field("news.id,news.wxaccount_id,news.keyword,news.description,news.title,news.url, news.sort, news.pictureid , pic.path as piclocal,pic.url as picremote") ->join('LEFT JOIN __PICTURE__ as pic ON pic.id = news.pictureid')
				 -> where($map)->order($order) -> select();
		
		if ($list === false) {
			$error = $this -> model -> getDbError();
			return $this -> apiReturnErr($error);
		}

		return $this -> apiReturnSuc($list);
	}
}
