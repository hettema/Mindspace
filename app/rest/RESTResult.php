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
 * Base class for a rest Result. This class will be decorated / Inherited to 
 * satisfy with other REST results.
 */
class RESTResult {
	var $success; //Status
	var $message; //Message , useful incase of failiure.

	public function __construct($status,$message) {
		$this->success = $status;
		$this->message = $message;
	}
	public function setResult($status,$message) {
		$this->success = $status;
		$this->message = $message;
		return $this;
	}
}
?>