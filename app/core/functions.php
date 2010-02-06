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


function __autoload($class)
{
    if (strpos($class, '/')!==false) {
        return;
    }
   
    $module = "core";
    if (strpos($class,"User") !== false && strpos($class,"User") == 0) {
    	$module = "user";
    } else if (strpos($class,"MYSQL")!== false && strpos($class,"MYSQL") == 0) {
    	$module = "mysql";
    } else if (strpos($class,"REST") !== false && strpos($class,"REST") == 0 ) {
    	$module = "rest";
    }
    $classFile = BP.DS."app".DS.$module.DS.$class.'.php';
    include_once($classFile);
}



function getKeywords($group) {
	global $gSession;
	global $gDBHelper;
	$serpInfo = array();
	$serpInfo['groupName'] = $group;
	$serpInfo['userId'] =  $gSession->userInfo['userId'];
	$res = $gDBHelper->selectInfo($serpInfo,'keyword_group');
	$keywords = array();
	if ($res[0]['keywords']) {
		$keywords = explode("\n",$res[0]['keywords']);
		
	}
	return $keywords; 
}
/**
 * Function to get the Search results from Yahoo! BOSS API
 * @param {String} query the search query
 */ 
function getBOSSResults(/*String*/$query) {
	$apikey = "SzEok7bV34F6oYZ.yODcfEltgfc.qcslpBvzljQh0cPW4K9D_aJXo3zIEakg.HTy7q7VqI3l";
	$searchResults = array();
	for ($idx = 0; $idx < 2; $idx++) {
		$thequery = urlencode($query);
		$url = 'http://boss.yahooapis.com/ysearch/web/v1/'.$thequery.'?appid='.$apikey.'&format=xml&count=50';
		$url .= "&start=".($idx*50);
		//echo "<!-- ".$url."-->";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		$results = new SimpleXmlElement($data, LIBXML_NOCDATA);
		$total = $results->resultset_web->attributes();
		$total = $total['totalhits'];
		$results = $results->resultset_web->result;
		$i = $idx * 50 + 1;

		foreach ($results as $theresult) {
			if( !empty($theresult->title) ){
				$searchResults[] = "".$theresult->url;
			}
		}
	}
	return $searchResults;
}
/***
 * Function to get search results from google SERP
 * We are doing  screen scraping of the SERP
 * @param {String}query The search query.
 * @param {String} engine The search engine.{www.google.com,www.google.de, etc.}
 * 
 */ 

function getGoogleResuls(/*String*/$query,/*String*/$engine){
	$url = "http://".$engine."/search?as_q=".urlencode($query)."&num=100&ft=i&as_qdr=all&as_occt=any&safe=images";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$resp = curl_exec($ch);
	curl_close($ch);
	preg_match_all('/<a[^>]+>*/', $resp, $matches);
	$matches = $matches[0];
	$results = array();
	$count = count($matches);
	for ($idx = 0; $idx < $count ; $idx++ ) {
		if (strpos($matches[$idx],'class=l') == true) {
			preg_match('/href="([^"]*)"/',$matches[$idx],$res);
			$results[] = $res[1];
		}
	}
	return $results;
}

/**
 * Dummy SERP to test the application without hitting web services.
 * 
 */
function getTestResults() {
	return  array(
"http://www.rei.com/",
"http://www.camping-gear-outlet.com/",
"http://www.gearforcamping.com/",
"http://www.shop4campinggear.com/",
"http://www.campman.com/",
"http://www.campsaver.com/",
"http://www.campinggeardepot.com/",
"http://gear.camping.com/",
"http://www.camping.com/",
"http://www.campmor.com/",
"http://www.familycampinggear.com/",
"http://www.campingstation.com/",
"http://www.shop4campinggear.com/camping-gear",
"http://www.ourcampinggear.com/",
"http://www.shop4campinggear.com/camping-gear",
"http://www.alpinecampingsupply.com/index.html",
"http://www.campinggearpro.com/",
"http://www.camping-gear.us/",
"http://www.shop4campinggear.com/camping-gear",
"http://www.summitcampinggear.com/",
"http://www.cabelas.com/",
"http://www.altrec.com/camp/",
"http://www.summitcampinggear.com/camping-gear.html",
"http://www.coleman.com/",
"http://www.gearcamping.us/",
"http://www.walmart.com/catalog/catalog.gsp?cat=4128",
"http://www.wenzel-camping-gear.com/",
"http://www.gaiam.com/category/eco-home-outdoor/outdoor/camping+gear.do",
"http://www.jcwhitney.com/Camping_Gear?ID=7;1101018373;1101018367;0;100001;Category;0;0;0;0;0",
"http://www.campingcomfortably.com/",
"http://www.camping-gear-outlet.com/camping-equipment-186.html",
"http://trailheadcampinggear.com/",
"http://www.healthyhikergear.com/index.html",
"http://www.camping-gear.net/index.html",
"http://www.campinggearstop.com/",
"http://www.ljcamping.com/index.html",
"http://www.campinggearguide.com/index.html",
"http://campinggearsolutions.com/",
"http://www.campinggear4you.com/index.html",
"http://whiterockcampinggear.com/",
"http://www.coleman.com/coleman/home.asp",
"http://www.campingworld.com/browse/skus/index.cfm?skunum=26269",
"http://geared4camping.com/",
"http://www.cabelas.com/camping.shtml",
"http://www.campgearpro.com/",
"http://www.cauinc.com/promotional/category/125",
"http://www.thefind.com/sports/info-3-season-tents-camping-gear",
"http://tntcampinggear.com/",
"http://campinggearstore.net/",
"http://www.mountaingear.com/",
"http://www.kodiakoutback.com/Camping-Hiking-Gear.aspx",
"http://www.usoutdoorstore.com/camping.htm");
}
/*
 * Dummy function to test the resilt counter
 */
function getTestResults2() {
	return  array(
"http://www.google.com/",
"http://www.yahoo.com/",
"http://www.more.com/",
"http://www.shop4campinggear.com/");

}
?>