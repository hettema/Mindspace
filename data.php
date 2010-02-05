<?php 
include_once('app/etc/Config.php');
include_once('app/core/functions.php');

$gSession = new UserSession();
//Invalid session -> Die !!!
if (!$gSession->isValid()) {
	$res = new RESTResult(false,"Invalid Login Credentials");
	echo json_encode($res);
	die;
}

include_once('app/mysql/MYSQLHelper.php');
$op = isset($_GET['op']) ? $_GET['op'] :"Invalid";

//Checks whether the operation is supported to the source of truth.
if (!RESTModule::isOperationSupported($op)) {
	$res = new RestResult(false,"Unsupported Operation.");
	echo json_encode($res);
	die;
}

/*
 * Initializing the factory Object
 */
$factory = RESTModuleFactory::getInstance();

$module = isset($_GET['mod']) ? $_GET['mod'] :"Invalid";
/*
 * Creating the necessary module to handle the REST Request
 */
$mod = $factory->createModule($module);
if ($mod == NULL) {
	$res = new RestResult(false,"Unsupported Module.");
	echo json_encode($res);
	die;
}

/*
 * Performing the rest operation and returning the result
 */
$value = isset($_GET['value']) ? $_GET['value'] :"";
$result = $mod->$op($value);
$result->module = $module;
$result->op = $op;
echo json_encode($result);

?>