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
 * Class for storing information about page/Domain and the number of occurance
 * in SERP
 * 
 */
function SERPInfo (path,weight) {
		if (path=="") {
			path = "/";
		}
		this.name = path;
		this.count = 1;
		this.weight = weight;
	
	this.getWeight= function() {
		return this.weight;
	}

}
/**
 * Class for handling vsarious category of SERP Data Each category will have a
 * domain list and page list, which are array of SERPInfo
 * 
 * @author Neo
 * 
 */
function SERPCategory () {
		this.pageList = [];
		this.domainList = [];
	
	/**
	 * Add a URL to the category.
	 * 
	 * @param String
	 *            url The URL from the SERP
	 * @param String
	 *            weight, The SERP weight to keep the relative positioning.
	 */
	this.addURL = function(url, weight) {
		
		// purl = preg_replace("/.*:\/\//","",url);
		// paths = preg_split("/\//",purl,2);
		
		var re = new RegExp('^(?:f|ht)tp(?:s)?\://([^/]+)', 'im');
		var result = url.match(re);
		var host = "";
		if (result != null && result.length > 1) {
			host =  result[1].toString();
		}


		var domainInfo = null;
		for (var idx = 0 ; idx < this.domainList.length; idx++) {
			if (host ==  this.domainList[idx].name) {
				domainInfo = this.domainList[idx];
				break;
			}
		}
		if (domainInfo == null) {
			domainInfo = new SERPInfo(host,weight);
			this.domainList.push( domainInfo);
		} else {
			domainInfo.count++;
		}
		var page = null;
		for (idx = 0; idx < this.pageList.length; idx++) {
			if (this.pageList[idx].name == url) {
				page = this.pageList[idx];
				break;
			}
		}
		if (page == null) {
			this.pageList.push(new SERPInfo(url,weight));
		} else {
			page.count++;
		}
		
	}
	/**
	 * Function for sorting the information.
	 */
 	function cmpDesc(m, n) {
	    if (m.count == n.count) {
	    	if (m.getWeight() == n.getWeight()) {
	    		return 0;
	    	} 
	    	return (m.getWeight() > n.getWeight()) ? -1 : 1;
	    }
	    return (m.count > n.count) ? -1 : 1;
	 }
	 /**
		 * Sorts the pagelist and domainList
		 */
	this.sort = function() {
		this.domainList.sort(cmpDesc);
		this.pageList.sort(cmpDesc);
		
	}
	
}
/**
 * Handles the SERP Result analyzing
 * 
 * @author Neo
 * 
 */
function ResultAnalyzer () {
	/**
	 * Categories for analyzing the SERP
	 * @var SERPCategory
	 */
	this.monsters = new SERPCategory();
	this.dominators =  new SERPCategory();
	this.players =  new SERPCategory();
	this.participants =  new SERPCategory();
	/**
	 * Status variable to show whether results are loaded.
	 * 
	 * @var bool
	 */
	this.resultsLoaded = false;
	
	/**
	 * Adds a set of urls to the Result analyzer.
	 * 
	 * @param array
	 *            results The URL list from SERP
	 */
	 this.addSERP = function(results) {
	
		for (var idx = 0; idx < results.length; idx++) {
			this.addURL(results[idx],idx);
		}
		
		this.resultsLoaded = true;
		
	}
	/**
	 * Adds a url , to various categories based on the positon on SERP
	 * 
	 * @param String
	 *            url The URK from SERP
	 * @param string
	 *            pos The position on SERP
	 */
	this.addURL = function(url, pos) {
		
		var processingList = [];
		
		if (pos < 3) {
			processingList.push( this.monsters);
		}
		if (pos < 10) {
			processingList.push(this.dominators);
		}
		if (pos < 20) {
			 processingList.push(this.players);
		}
		processingList.push( this.participants);
		
		for (var pIdx = 0; pIdx < processingList.length; pIdx++ ) {
			var weight = 100 - (pos); 
			processingList[pIdx].addURL(url,weight);
			
		}
	}
	/**
	 * Sorts the results in various categories
	 */
	this.sortResult = function() {
		this.monsters.sort();
		this.dominators.sort();
		this.players.sort();
		this.participants.sort();
	}	
	/*
	 * Return the JSON Structure; might be needed in future @return String JSON
	 * formated output
	 */
	
}

