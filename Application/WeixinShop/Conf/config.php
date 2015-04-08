<?php
/**
 * (c) Copyright 2014 hebidu. All Rights Reserved. 
 */
 

return array(
	'DEFAULT_THEME'=>'default',
	// 伪静态配置
	'URL_HTML_SUFFIX'=>'shtml'	,
    // 数据库配置
    'DB_TYPE'                   =>  'mysql',
    'DB_NAME'                   =>  'db_2015wytch', //微信api数据库
    'DB_PORT'                   =>  '3306',
    'DB_PREFIX'                 =>  'wytch_',
   	
    'WXPAY_CONFIG'=>array(
		'appid'=>'wx58aea38c0796394d',
		'appsecret'=>'3e1404c970566df55d7314ecfe9ff437',
		'mchid'=>'10027619',
		'notifyurl'=>'http://2.test.8raw.com/index.php/Shop/WxpayNotify/index',
		'key'=>'755c9713b729cd82467ac592ded397ee',//在微信发送的邮件中查看,patenerkey
		'jsapicallurl'=>'http://2.test.8raw.com/index.php/Shop/Orders/pay?showwxpaytitle=1',
		'sslcertpath'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
		'sslkeypath'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
	),

	
);
