<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
$needCheckFunctions = array(
	array('name'=>'fsockopen','msg'=>'请开启fsockopen'),
	array('name'=>'imagecreate','msg'=>'请开启GD库以支持imagecreate'),
	array('name'=>'stream_socket_client','msg'=>'服务器需要支持stream_socket_client函数'),
	
);


echo "========测试开始=================<br/>";

$flag = true;
foreach($needCheckFunctions as $vo){

	if(!function_exists($vo['name'])) {
		echo ('<span class="color:#FF0000;">'.$vo['msg'].'</span><br/>');
		$flag = false;
	}
	
}
if($flag){
	echo "全部通过！<br/>";
}

echo "========测试结束=================<br/>";
