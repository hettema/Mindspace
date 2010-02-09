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

class RESTModule {
	var $module_name; // This will be the corresponding DB Module
	var $result = null;
	var $dbHelper = null;
	var $userInfo = null;
	public function __construct($name) {
		if (!$name) {
			$name = "Default";
		}
		$this->module_name = $name;

		$this->result = new RESTResult(false,"Not Inititalized");
	}
	public function setDBHelper($helper) {
		$this->dbHelper = $helper;
	}
	public function setUserInfo($info) {
		$this->userInfo = $info;
	}
	public function getName() {
		return $this->module_name;
	}

	public function setResult($result,$message) {
		$this->result->success = $result;
		$this->result->message = $message;
		return $this->result;
	}
	/**
	 * functions that will need to be overrided by the inherited modules.
	 */
	public function add() {
		return $this->setResult(false,$this->module_name." Module Doesn't support method Add");
	}
	public function remove() {
		return $this->setResult(false,$this->module_name." Module Doesn't support method Remove");
	}
	public function get() {
		return $this->setResult(false,$this->module_name." Module Doesn't support method Get");
	}
	public function update() {
		return $this->setResult(false,$this->module_name." Module Doesn't support method Update");
	}
	public static function isOperationSupported($op) {
		if ($op == 'add' || $op == 'remove' || $op == 'update' || $op == 'get') {
			return true;
		}
		return false;
	}
};
?>