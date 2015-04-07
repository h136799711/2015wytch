<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Model;

use Think\Model;

class LogModel extends Model{
	
    protected $tablePrefix = 'common_';
	
	protected $_auto = array(
		array('timestamp','time',1,'function')
	);
	
	
}
