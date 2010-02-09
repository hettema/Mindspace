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


/**
 * @class This class Stored the domain name and occurance counts for search
 * categories. Monsters,Domincators,Players and Participants
 * @author Neo
 *
 */
class DomainInfo {
	var $domainName;
	var $occurances;
	function __construct($name) {
		$this->domainName = $name;
		$this->occurances = array();
	}
}

/**
 * @class This class aggregates the data needed for creating the data needed for drawing the graph
 *  To keep the data flow optimized we have a single array of timestamps and the engines used for
 *  making the search.
 *
 * @author Neo
 *
 */

class DomainPerformanceResult extends RESTResult implements DomainFilter{

	/**
	 *
	 * @var DomainInfo[]
	 */
	var $domainInfo;
	/**
	 * Array of timestamps on which the searches were made.
	 * @var array of integer
	 */
	var $timestamps;
	/**
	 * Array of engines.
	 * @var array of strings
	 */
	var $engines;

	public function __construct($success,$msg) {
		$this->timestamps = array();
		$this->domainInfo = array();
		$this->engines = array();
		parent::__construct($success,$msg);
	}
	/**
	 * Implementing the DomainFilter interface.
	 */
	public function isPresent($url) {

		$purl = preg_replace("/.*:\/\//","",$url);
		$paths = preg_split("/\//",$purl,2);

		for ($idx = 0; $idx < count($this->domainInfo); $idx++) {
			if ($this->domainInfo[$idx]->domainName == $paths[0]) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Adding the domains to the domainList
	 */
	public function setDomains($domainList) {
		$this->domainInfo = array();
		for ($idx = 0; $idx < count($domainList); $idx++) {
			$this->domainInfo[] = new DomainInfo($domainList[$idx]);
		}

	}
	/**
	 * Aggregates the various SERPAnalyzer info over timeperiod to construct the data set.
	 */
	public function addAnalyzerInfo($serpAnalyzer,$ts,$engine) {
		$this->engines[] = $engine;
		$this->timestamps[] = $ts * 1000;
		for ($idx = 0; $idx < count($this->domainInfo); $idx++) {
			$name = $this->domainInfo[$idx]->domainName;
			$occ = array();
			$occ[] = $serpAnalyzer->monsters->getDomainCount($name);
			$occ[] = $serpAnalyzer->dominators->getDomainCount($name);
			$occ[] = $serpAnalyzer->players->getDomainCount($name);
			$occ[] = $serpAnalyzer->participants->getDomainCount($name);
			$this->domainInfo[$idx]->occurances[] = $occ;
		}
	}

}

class DomainAnalyzer {
	var $keywordList;
	var $result;

	public function __construct() {
		$this->result = new DomainPerformanceResult(false,"");
	}

	private function setKeywordList($keywordList){
		$this->keywordList = $keywordList;
	}
	/*
	 * Does result analysis for a particular timestamp.
	 */
	public function processForTimestamp($ts,$engine) {

		$tm = $ts - ($ts % SAMPLING_INTERVAL);
		global $gDBHelper;
		$resultAnalyzer = new SERPAnalyzer();
		$resultAnalyzer->addDomainFilter($this->result);

		for ($idx = 0; $idx < count($this->keywordList); $idx++) {
			$serpInfo = array();
			$serpInfo['engineId'] = $engine;
			$serpInfo['keyword'] = $this->keywordList[$idx];
			$serpInfo['timestamp'] = $tm;
			$res = $gDBHelper->selectInfo($serpInfo,'search_results');
			$resultAnalyzer->addSERP(json_decode($res[0]['SERP']));
		}
		$this->result->addAnalyzerInfo($resultAnalyzer,$ts,$engine);

	}
	/*
	 * Analyze the Domain preference for a group for a keyword group
	 */
	function analyze($group,$category) {
		global $gDBHelper;
		global $gSession;

		/*
		 * Setting keyword list.
		 */
		$this->setKeywordList(getKeywords($group));

		if (count($this->keywordList) == 0) {
			$this->result->setResult(false,"No keywords present in particular group");
			return;
		}
		/*
		 *  Setting domain list.
		 */
		$serpInfo = array();
		$serpInfo['categoryName'] = $category;
		$serpInfo['userId'] =  $gSession->userInfo['userId'];
		$res = $gDBHelper->selectInfo($serpInfo,'domain_interests');
		$domains = "";
		if ($res[0]['domains']) {
			$domains = $res[0]['domains'];
		}
		if ($domains == '') {
			$this->result->setResult(false,"The Domain List is empty");
			return;
		}
		$this->result->setDomains(explode("\n",$domains));

		/*
		 * getting the search info for particular group for the user.
		 */
		$serpInfo = array();
		$serpInfo['groupName'] = $group;
		$serpInfo['userId'] =  $gSession->userInfo['userId'];
		$res = $gDBHelper->selectInfo($serpInfo,'search_info');
		for ($idx = 0; $idx < count($res); $idx++) {
			$this->processForTimestamp($res[$idx]['timestamp'], $res[$idx]['engineId']);
		}
		$this->result->setResult(true,"Success");

	}
}


?>