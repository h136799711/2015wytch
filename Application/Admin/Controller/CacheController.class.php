<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

class CacheController extends AdminController {

	/**
	 * 清除所有缓存
	 */
	public function clearAll() {
		$this->clearHTML();
		$this->clearTEMP();
		$this->clearData();
		$this->display();
	}
	/**
	 * 应用模板缓存
	 */
	public function clearHTML() {
		$this->rmdirr(CACHE_PATH);
	}

	/**
	 * 缓存
	 */
	public function clearData() {
		$this->rmdirr(DATA_PATH);
	}
		
	/**
	 * 临时，S缓存
	 */
	public function clearTEMP() {
		$this->rmdirr(TEMP_PATH);
	}
	
	//处理方法
	public function rmdirr($dirname) {
		if (!file_exists($dirname)) {
			return false;
		}
		if (is_file($dirname) || is_link($dirname)) {
			return unlink($dirname);
		}
		$dir = dir($dirname);
		if ($dir) {
			while (false !== $entry = $dir -> read()) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				//递归
				$this -> rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
			}
		}
		$dir -> close();
		return rmdir($dirname);
	}

}
