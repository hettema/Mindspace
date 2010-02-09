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