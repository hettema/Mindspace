<?php 
/**
 * Base class for a rest Result. This class will be decorated / Inherited to 
 * satisfy with other REST results.
 */
class RESTResult {
	var $success; //Status
	var $message; //Message , useful incase of failiure.

	public function __construct($status,$message) {
		$this->success = $status;
		$this->message = $message;
	}
	public function setResult($status,$message) {
		$this->success = $status;
		$this->message = $message;
		return $this;
	}
}
?>