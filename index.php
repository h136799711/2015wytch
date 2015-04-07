<?php
// .-----------------------------------------------------------------------------------
// | 
// | WE TRY THE BEST WAY
// | Site: http://www.gooraye.net
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2012-2014, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 是否调试模式
define('APP_DEBUG',true);

require_once('./auth.php');

// 运行时文件
define("APP_PATH","./Application/");

// 框架目录
define("THINK_PATH",realpath("./Core/").'/');

// 运行时文件
define("RUNTIME_PATH","./Runtime/");

// 第三方类库目录
//define("VENDOR_PATH","./Vendor/");

// 加载
require "./Core/ThinkPHP.php";