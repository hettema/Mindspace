<?php 
/**
 * @class for managing user session. validates the login credentials with 
 * help of UserEntity. If its a valid login, creates a session variable to
 * keep track of the user login credentials
 * @author Neo
 *
 */
class UserSession
{
	
    var $sessionId;
    var $userInfo;
    
    function __construct()
    {
        session_module_name('files');
        session_save_path(SESSION_SAVE_PATH); 
        session_start();
        $this->sessionId = session_id(); 
        
        if (isset($_SESSION[$this->sessionId]) ){
        	$this->loadSessionData();
        } 
    }
    public function isValid()
    {
    	return !empty($_SESSION[$this->sessionId]);
    }
   
    public function getUserInfo() {
    	return $this->userInfo;
    }
    public function validateLogin($username,$password)
    {
    	global $gMessage;
        if(empty($username) || empty($password)) {
        	$gMessage = "Invalid User Name Or Password"; 
        	return false;
        }
        $user = new UserEntity();
        if($user->login($username, $password)) {
            $this->userInfo = $user->info;
            $this->setSessionData();
            return true;
        }
        $gMessage = "Invalid User Name Or Password"; 
        return false;
    }
   
    public function logOut()
    {
        if(!empty($_SESSION[$this->sessionId])) {
            unset($_SESSION[$this->sessionId]);
        }   
        session_destroy();
    }
    
    private function setSessionData()
    {
        $_SESSION[$this->sessionId] = $this->userInfo;
    }
    private function loadSessionData() 
    {
    	$this->userInfo = $_SESSION[$this->sessionId];
    }
    public function reloadSession($userInfo) {
    	
    	$this->userInfo = $userInfo;
    	$this->setSessionData();
    }
   
}

?>