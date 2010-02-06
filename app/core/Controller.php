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
 * The Route configuration, creates a configutation list of action_id to page map.
 * also indicates whether signing in is required for performing an action.
 * 
 * @author Neo
 *
 */
final class RouteConfig {
	static $configList = null;

	private static function init() {

		self::$configList = array();
		self::addConfig("login","template/core/login.phtml",false);
		self::addConfig("login_submit","",false);
		self::addConfig("new_account","template/core/new_account.phtml",false);
		self::addConfig("new_account_submit","template/core/message.phtml",false);
		self::addConfig("reset_password","template/core/reset_password.phtml",false);
		self::addConfig("reset_password_submit","template/core/message.phtml",false);




		self::addConfig("profile_change_submit","template/core/manage_profile.phtml",true);
		self::addConfig("result_analyzer","template/core/result_analyzer.phtml",true);
		self::addConfig("result_analyzer_p","template/core/result_analyzer_p.phtml",true);
		self::addConfig("domain_interests","template/core/domain_interests.phtml",true);
		self::addConfig("historical_data","template/core/historical_data.phtml",true);
		self::addConfig("manage_profile","template/core/manage_profile.phtml",true);
		self::addConfig("manage_groups","template/core/manage_groups.phtml",true);
		self::addConfig("scheduler","template/core/scheduler.phtml",true);
		self::addConfig("logout","template/core/login.phtml",true);
		self::addConfig("home","template/core/home.phtml",true);
	}

	private static function addConfig($key,$page,$private) {
  		$conf = new stdClass;
  		$conf->page = $page;
  		$conf->private = $private;
  		$conf->name = $key;

  		self::$configList[$key]= $conf;
  	}

	public static function getRouteConfig() {
		if (self::$configList == null) {
			self::init();
		}
		return self::$configList;

	}
}

/**
 * The Menu Item ID to text mapping. this is used for creating the left menu;
 * @author Neo
 *
 */
class MenuItem {
	var $name;
	var $link;
	function __construct($link,$name) {
		$this->name = $name;
		$this->link = $link;
	}
}
/*
 * Initializing the Menu
 * 
 */
$gMenuItems = array();
$gMenuItems[] = new MenuItem("home","Home");
$gMenuItems[] = new MenuItem("manage_groups","Manage Group");
$gMenuItems[] = new MenuItem("result_analyzer","Result Analyzer");
$gMenuItems[] = new MenuItem("domain_interests","Domain Interests");
$gMenuItems[] = new MenuItem("historical_data","Historical Data");
//$gMenuItems[] = new MenuItem("scheduler","Scheduler");
$gMenuItems[] = new MenuItem("manage_profile","Manage Profile");
$gRouteInfo =  RouteConfig::getRouteConfig();
?>