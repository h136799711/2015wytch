<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


/**
 * 插件调用
 * 
 */
function pluginCall($pluginname,$vars){
	return apiCall('Weixin/'.$pluginname.'/process', $vars,"Plugin");
}

/**
 * 获取session中的wxaccount_id
 * @return -1 无效  ；大于0 有效
 */
function getWxAccountID(){
	if(session("?weixin_wxaccount") && is_array(session("weixin_wxaccount"))){
		return session("weixin_wxaccount")['id'];
	}else{
		return -1;
	}
//	return session("wxaccount_id");
}


//===============================================================


