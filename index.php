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
include_once('app/etc/Config.php');
include_once('app/mysql/MYSQLHelper.php');
include_once('app/core/Controller.php');
include_once('app/core/functions.php');


$gSession = new UserSession();
$gMessage = "";
$gErrorMessage = "";
if (empty($_GET['go'])) {
	if ($gSession->isValid()) {
		$_GET['go'] =  "home" ;
	} else {
		$_GET['go'] =  "login";
	}
}

/*
 * Now we are sure that we have a correct navigation object
 */
$infoPresent = isset($gRouteInfo[$_GET['go']]);
if ($infoPresent == false ) {
	if ($gSession->isValid()) {
		$_GET['go'] =  "home" ;
	} else {
		$_GET['go'] =  "login";
	}
}
$info = $gRouteInfo[$_GET['go']];


if ($info->private == true && !$gSession->isValid()) {
	$info = $gRouteInfo['login'];
}
if ($_GET['go'] == 'logout') {
	$gSession->logout();
	$info = $gRouteInfo['login'];
}

if ($info->private == false && $gSession->isValid()) {
	$info = $gRouteInfo['home'];
}

if ($_GET['go'] == 'new_account_submit') {
	 $user = new UserEntity();
	 $user->createNewAccount();
} else if ($_GET['go'] == 'reset_password_submit') {
	$user = new UserEntity();
	$user->resetPassword();
} else if ($_GET['go'] == 'login_submit') {
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	if ($gSession->validateLogin($username,$password)) {
		$info = $gRouteInfo['home'];
	} else {
		$info = $gRouteInfo['login'];
	}
} else if ('profile_change_submit' == $_GET['go']){
	$user = new UserEntity();
	$gSession->reloadSession($user->changeProfile($gSession->userInfo['email']) );
}

//print_r($gSession->userInfo);// "logout happening...";

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Mindspace SEO Tool</title>
<link rel="stylesheet" type="text/css" href="template/css/analyzer.css" />
<link rel="stylesheet" type="text/css" href="template/css/layout.css" />
<link rel="stylesheet" type="text/css" href="template/3rdparty/yui/reset-fonts-grids/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" href="template/3rdparty/yui/base/base-min.css" />
<link type="text/css" href="template/3rdparty/jquery/css/redmond/jquery-ui-1.7.2.custom.css"	rel="stylesheet" />
<script type="text/javascript" src="template/3rdparty/jquery/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="template/3rdparty/jquery/js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="template/js/Transaction.js"></script>
<!--[if IE]><script language="javascript" type="text/javascript" src="template/3rdparty/jquery/js/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="template/3rdparty/jquery/js/jquery.flot.js"></script>



</head>
<body class="yui-skin-sam">
<div id="doc3" class="yui-t2" >

<div id="hd">
<?php
if ($info->private) {
?>
	<div align="right"><a href="index.php?go=logout">logout</a></div>
<?php }
?>
		<h1>SERP Analyzer</h1>
	</div>
<div id="bd"  style="background:rgb(85,142,213)">

<?php if (!$info->private) { ?>
	<div class="yui-gc" style="height:600px">
	    <div class="yui-u first" align="center">
	        <p></p>
	    </div>
	    <div class="yui-u">
			<?php include_once($info->page); ?>
		</div>
	</div>
<?php } else {

	$gUser = $gSession->userInfo;
	?>
	<div class="yui-b menu-container" >
<ul class="menu-box">
		<?php
for ($idx = 0; $idx < count($gMenuItems); $idx++) {
	$menu = $gMenuItems[$idx];
	$selected = "";
	if ($menu->link == $info->name) {
		$selected = "selected";
	}
echo "<li class=\"menu-item $selected \"><a href=\"index.php?go=$menu->link\">$menu->name</a></li>\n";
}
?>
</ul>
</div>
	<div id="yui-main" >
		<div class="yui-b main-content">
				<?php include_once($info->page); ?>
		</div>
	</div>

<?php } ?>
</div>
<div id="ft" align="center">
	<p>Copyright 2009</p>
</div>
</div>

<div id="dialog" title="Message">
			<p id="dialog-message">message</p>
</div>
</body>
<script>

$('#dialog').dialog({
	autoOpen: false,
	width: 300,
	dialogClass: 'alert',
	buttons: {
		"Ok": function() {
			$(this).dialog("close");
		}
	},
	modal: true
});

function showDialog(msg) {
	$('#dialog-message').html(msg);
	$('#dialog').dialog('open');
}

if (typeof initModule == 'function' ) {
   initModule();
}
</script>

</html>

