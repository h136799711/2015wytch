<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 商品类目模型
 */
class CategoryModel extends Model{
		
	protected $_validate = array(
		array('name','require','类目名称必须',self::MODEL_INSERT)
	);
	
}

