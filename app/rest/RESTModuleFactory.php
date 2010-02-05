<?php
/**
 * Factory for handling the REST Modules
 * @author Neo
 *
 */
class RESTModuleFactory {


	private $registeredModules = array();

	static private $instance = NULL;

	private function __construct() {
		$this->registerModule('keyword_group','RESTModuleKeywordGroup');
		$this->registerModule('domain_interests','RESTModuleDomainInterests');
		//$this->registerModule('search_results','RESTModuleSearh'); 
		//Rest approach for pulling data is network hungry.
		//$this->registerModule('search_info','RESTModuleSearchInfo');
	}

	static function getInstance()
	{
		if(self::$instance == NULL)
		{
			self::$instance = new RESTModuleFactory();
		}
		return self::$instance;
	}
	public function registerModule($name,$className) {
		$this->registeredModules[$name] = $className;
	}
	public function createModule($name) {
		$name = strtolower($name);
		if (isset($this->registeredModules[$name])) {
			global $gSession;
			global $gDBHelper;
			$mod = new $this->registeredModules[$name]($name);
			$mod->setUserInfo($gSession->getUserInfo());
			$mod->setDBHelper($gDBHelper);
			return $mod;
		}
		return NULL;
	}

}

?>
