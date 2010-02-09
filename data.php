<?php
/**
 * Copyright 2009, 2010 hette.ma.
 *
 * This file is part of Mindspace.
 * Mindspace is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.Mindspace is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public
 * License for more details.You should have received a copy of the GNU General Public License
 * along with Mindspace. If not, see <http://www.gnu.org/licenses/>.
 *
 *  credits
 * ----------
 * Idea by: Garrett French |    http://ontolo.com    |     garrett <dot> french [at] ontolo (dot) com
 * Code by: Alias Eldhose| http://ceegees.in  | eldhose (at) ceegees [dot] in
 * Initiated by: Dennis Hettema    |    http://hette.ma    |     hettema (at) gmail [dot] com
 */
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