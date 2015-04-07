<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Think\Log\Driver;
/**
 * 数据库方式日志驱动
 *    CREATE TABLE common_log (
 *      id long not null,
 *      timestamp int(11) NOT NULL,
 *      REMOTE_ADDR varchar(255),
 *      REQUEST_URI varchar(1024),
 * 		context  text,
 * 		info text,
 *      UNIQUE KEY `id` (`id`)
 *    );
 * 
 */
class Db {

	protected $config = array(
		'dsn'=>'',//数据库配置
	 	'table' => 'log', 
	 	'prefix' => 'common_'
	);
	protected $handler;
	// 实例化并传入参数
	public function __construct($config = array()) {
		$this -> config = array_merge($this -> config, $config);
		$this -> config = array_merge($this -> config, C('LOG_DB_CONFIG'));
		$this -> handler = \Think\Db::getInstance($this->config['dsn']);
	}

	/**
	 * 日志写入接口
	 * @access public
	 * @param string $log 日志信息
	 * @param string $destination  写入目标
	 * @return void
	 */
	public function write($log, $destination = '') {
		$context = 'COOKIE:' . serialize($_COOKIE);
		$context .= 'SESSION:' . serialize($_SESSION);
		$context .= 'POST:' . serialize($_POST);
		$context .= 'GET:' . serialize($_GET);
		$context .= 'SERVER:' . serialize($_SERVER);
//		$context = 'context';
		$sql = 'INSERT INTO `' . $this -> config['prefix'].$this -> config['table'] . '` (`timestamp`,`remote_addr`,`request_uri`,`context`,`info`) VALUES (\'' . time() . '\',\'' . $_SERVER['REMOTE_ADDR'].'\',\''. $_SERVER['REQUEST_URI'] . '\',\'' . $context . '\',\'' . $log . '\')';
//		dump($sql);
		$this -> handler -> execute($sql);
	}

}
