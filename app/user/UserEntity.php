<?php
/**
 * Distinguishes a User entity makes the corresponding DB calls to obtaining the data
 * validating Login
 * @author Neo
 *
 */
Class UserEntity
{
    private $user_table = 'admin_user';
    private $_data = array();
    var $info = array();
    function  __construct()
    {

    }
    public function login($username, $password)
    {
    	global $gDBHelper;

    	$selectInfo = array();
    	$selectInfo['userName'] = $username;
    	$userInfo = $gDBHelper->selectInfo($selectInfo,$this->user_table);
    	if(empty($userInfo)) {
            return false;
        }
        $userInfo = $userInfo[0];
        $this->info = $userInfo;
        if(md5($password) != $userInfo['password']) {
            return false;
        }

        return true;
    }

    private function getRandomString($len, $chars=null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }

    private function setUserInfo($userInfo)
    {
        $this->_data = $userInfo;
        return $this;
    }

    public function getData($key)
    {
        if(!empty($this->_data[$key])) {
            return $this->_data[$key];
        }
        return false;
    }

    public function createNewAccount()
    {
    	$createInfo = array();
    	$createInfo['userName'] = $_POST['username'];
    	$createInfo['password'] = $_POST['password'];
    	$createInfo['cpassword'] = $_POST['c-password'];
  
        $createInfo['firstName'] = $_POST['firstname'];
        $createInfo['lastName'] = $_POST['lastname'];
        $createInfo['email'] = !empty($_POST['email']) ? $_POST['email'] : strstr($_POST['username'], '@') ? $_POST['username'] : '' ;

		global $gMessage;
		
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",
			$createInfo['email'])){
			$gMessage = "Invalid email</center>";
			return;
		} 
        if ($createInfo['password'] != $createInfo['cpassword']) {
        	$gMessage = "Password mismatch";
        	return;
        }
        
        if($this->checkExistingUserName($createInfo['userName'])) {
           	$gMessage =  'The username you have specified is already registerd. Please try another username';
            return false;
        }
        $createInfo['created'] = time();
        $createInfo['accFlag'] = 'new-account';
        $createInfo['accFlag'] = 1;

        global $gDBHelper;
        $gDBHelper->insertInfo($createInfo,$this->user_table);
        $gMessage  = "Account successfully created";
        //$this->login($username, $password);
        $this->sendNewAccountEmail($createInfo['firstName'], 
        	$createInfo['email'],$createInfo['password']);
    }

    public function checkExistingUserName($userName)
    {
    	global $gDBHelper;
    	$sel['userName'] = $userName;
        $userInfo = $gDBHelper->selectInfo($sel,$this->user_table);
        if(!empty($userInfo[0])) return true;
        return false;
    }

    public function changeProfile($email) {
    	$password  = $_POST['password'];
        $changeInfo['firstName'] = $_POST['firstname'];
        $changeInfo['lastName'] = $_POST['lastname'];
        if ($password != '') {
        	$changeInfo['password'] = $_POST['password'];
        }
        $cond['email'] = $email;
        global $gDBHelper;
        $gDBHelper->updateInfo($changeInfo,$cond,$this->user_table);
        $sel['email'] = $email;
    	$userInfo = $gDBHelper->selectInfo($sel,$this->user_table);
        return $userInfo[0];
    }
    public function resetPassword()
    {
        $email = $_POST['email'];
        $this->loadUserByEmail($email);
        global $gMessage;
        if(!isset($this->info->email)) {
            $gMessage = 'Unable to find the email address in our database';
            return false;
        }
        $passwordNew = $this->getRandomString(8);
        global $gDBHelper;
        $set['password'] = $passwordNew;
        $cond['email'] = $email;
        $userInfo = $gDBHelper->updateInfo($set,$cond,$this->user_table);
        $this->sendNewPasswordEmail($email,$passwordNew);
        $gMessage = "Password succcessfully send to email address ".$email;
        echo "<!-- $passwordNew -->";
    }

    private function loadUserByEmail($email)
    {
    	$sel['email'] = $email;
    	global $gDBHelper;
        $userInfo = $gDBHelper->selectInfo($sel,$this->user_table);
        if(empty($userInfo[0])) return false;
        $this->info = $userInfo[0];
        return true;
    }


    private function sendNewAccountEmail($name,$email, $password)
    {
    	$this->sendMail($email,"New Account","Hi $name,  \n please login to the account with username:$email pass: $password");
    	
    }

    private function sendNewPasswordEmail($email,$password)
    {
		$this->sendMail($email,"Password recovery"," The new password generated is ".$password);

    }
    private function sendMail($to,$sub,$msg) {
    	mail($to,$sub,$msg);
    }
}

?>
