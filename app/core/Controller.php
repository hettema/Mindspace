<?php
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