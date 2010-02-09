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
 * Class for storing information about page/Domain and the number of occurance in SERP
 *
 */
class SERPInfo {
	/**
	 * The name of the weighted entity
	 * @var String
	 */
	public $name;
	/**
	 * The number of appearance.
	 * @var number
	 */
	public $count;
	/*
	 * The highest positoin on an SERP
	 */
	private $weight;
	public function __construct($path,$weight) {
		if ($path=="") {
			$path = "/";
		}
		$this->name = $path;
		$this->count = 1;
		$this->weight = $weight;
	}
	public function getWeight() {
		return $this->weight;
	}

};

/**
 * Class for handling vsarious category of SERP Data
 * Each category will have a domain list and page list, which are array of SERPInfo
 * @author Neo
 *
 */
class SERPCategory {
	/**
	 * Domain List
	 * @var array
	 */
	public $domainList;
	/**
	 * Page / URL List
	 * @var array
	 */
	public $pageList;

	public function __construct() {
		$this->pageList = array();
		$this->domainList = array();
	}
	/**
	 * Add a URL to the category.
	 * @param String $url The URL from the SERP
	 * @param String $weight, The SERP weight to keep the relative positioning.
	 */
	public function addURL($url, $weight) {

		$purl = preg_replace("/.*:\/\//","",$url);
		$paths = preg_split("/\//",$purl,2);

		$count = count($this->domainList);
		$domainInfo = null;
		for ($idx = 0 ; $idx < $count; $idx++) {
			if ($paths[0] ==  $this->domainList[$idx]->name) {
				$domainInfo = $this->domainList[$idx];
				break;
			}
		}
		if ($domainInfo == null) {
			$domainInfo = new SERPInfo($paths[0],$weight);
			$this->domainList[] = $domainInfo;
		} else {
			$domainInfo->count++;
		}
		$count = count($this->pageList);
		$page = null;
		for ($idx = 0; $idx < $count; $idx++) {
			if ($this->pageList[$idx]->name == $url) {
				$page = $this->pageList[$idx];
				break;
			}
		}
		if ($page == null) {
			$this->pageList[] =new SERPInfo($url,$weight);
		} else {
			$page->count++;
		}

	}
	/**
	 * Function for sorting the information.
	 */
 	function cmpDesc($m, $n) {
	    if ($m->count == $n->count) {
	    	if ($m->getWeight() == $n->getWeight()) {
	    		return 0;
	    	}
	    	return ($m->getWeight() > $n->getWeight()) ? -1 : 1;
	    }
	    return ($m->count > $n->count) ? -1 : 1;
	 }
	 /**
	  * Sorts the pagelist and domainList
	  */
	public function sort() {
		usort($this->domainList, array('SERPCategory','cmpDesc'));
		usort($this->pageList, array('SERPCategory','cmpDesc'));

	}

	public function getDomainCount($domain) {
		for ($idx = 0 ; $idx < count($this->domainList); $idx++) {
			if ( $this->domainList[$idx]->name == $domain) {
				return $this->domainList[$idx]->count;
			}
		}
		return 0;
	}
}


/**
 * Handles the SERP Result analyzing
 *
 * @author Neo
 *
 */
class SERPAnalyzer {

	/**
	 * Categories for analyzing the SERP
	 * @var SERPCategory
	 */
	public $monsters;
	public $dominators;
	public $players;
	public $participants;

	private $domainFilter;

	/**
	 * Status variable to show whether results are loaded.
	 * @var bool
	 */
	public $resultsLoaded;

	/**
	 * Initializes varous categories
	 *
	 */
	public function  __construct() {

		$this->monsters = new SERPCategory();
		$this->dominators =  new SERPCategory();
		$this->players =  new SERPCategory();
		$this->participants =  new SERPCategory();
		$this->resultsLoaded = false;
	}
	/**
	 * Adds a set of urls to the Result analyzer.
	 *
	 * @param array $results The URL list from SERP
	 */
	public function addSERP($results) {
		$count = count($results);

		for ($idx = 0; $idx < $count; $idx++) {
			$this->addURL($results[$idx],$idx);
		}
		if ($count >0) {
			$this->resultsLoaded = true;
		}
	}
	/**
	 * Adds a url , to various categories based on the positon on SERP
	 *
	 * @param String $url The URK from SERP
	 * @param string $pos The position on SERP
	 */
	private function addURL($url, $pos) {

		$processingList = array();

		if ($this->domainFilter && !$this->domainFilter->isPresent($url) ){
			return;
		}

		if ($pos < 3) {
			$processingList[] = $this->monsters;
		}
		if ($pos < 10) {
			$processingList[] = $this->dominators;
		}
		if ($pos < 20) {
			 $processingList[] = $this->players;
		}
		$processingList[] = $this->participants;

		for ($pIdx = 0; $pIdx < count($processingList); $pIdx++ ) {
			$weight = 100 - ($pos);
			$processingList[$pIdx]->addURL($url,$weight);

		}
	}
	/**
	 * Adding a domain filter helps us to process the data for the domains we have interest in.
	 * This optimization strategy allows us to keep the processing data set small and increase
	 * the overall efficiency of the application.
	 */
	public function addDomainFilter($filter) {
		$this->domainFilter = $filter;

	}
	/**
	 *Sorts the results in various categories
	 */
	public function sortResult() {
		$this->monsters->sort();
		$this->dominators->sort();
		$this->players->sort();
		$this->participants->sort();
	}


	/*
	 * Return the JSON Structure; might be needed in future
	 * @return String JSON formated output
	 */
	public function printInfo() {
		echo json_encode($this);
	}

}
?>