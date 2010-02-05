<?php
$connectDb['host'] = 'localhost';
$connectDb['port'] = 3306;
$connectDb['user'] = 'serp';
$connectDb['pass'] = 'test';
$connectDb['datb'] = 'SERP';
$connectDb['charSet'] = 'utf8';

date_default_timezone_set("Asia/Calcutta");
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(dirname(__FILE__))));
define('SAMPLING_INTERVAL', 6 * 3600);
define('SESSION_SAVE_PATH',BP . DS . 'sessions');
//error_reporting(E_ALL);
?>