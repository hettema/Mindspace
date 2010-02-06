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
 * Code by: Eldhose C G | http://ceegees.in  | eldhose (at) ceegees [dot] in
 * Initiated by: Dennis Hettema    |    http://hette.ma    |     hettema (at) gmail [dot] com
 */



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
