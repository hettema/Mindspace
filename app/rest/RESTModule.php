<?php
/**
 * Base Class for a REST Module.
 * @author Neo
 *
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