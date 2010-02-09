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
 * REST Module for handling the keyword group operations.
 * @author Neo
 *
 */
class RESTModuleKeywordGroup extends RESTModule {

	/**
	 * Utility function for validating a group name and showing appropriate error messages
	 *
	 */
	private function checkGroupName($groupName,$ifExistsThenError) {
		if ($groupName == '') {
			$this->setResult(false,"Please select a group");
			return false;
		}
		if (false) {
			return $this->setResult(false,"Invalid group Name; please use A-Z or a-z or _ or  0-9 for creating group names");
		}
		$selectInfo = array();
		$selectInfo['groupName'] = $groupName;
		$selectInfo['userId'] = $this->userInfo['userId'];
		$res = $this->dbHelper->selectInfo($selectInfo,$this->module_name);
		$res = $res[0];

		if ($ifExistsThenError) {
			if (isset($res['groupName'])) {
				$this->setResult(false,"Group Name Already present");
				return false;
			}
		} else {
			/*If doesn't exist - then error; */
			if (!isset($res['groupName'])) {
				$this->setResult(false,"Invalid Group Name");
				return false;
			}
		}
		return true;

	}
	/**
	 * Adding a new group name
	 */
	public function add($value) {
		if ($this->checkGroupName($value,true)) {
			$inserInfo = array();
			$insertInfo['groupName'] = $value;
			$insertInfo['keywords'] = '';
			$insertInfo['userId'] = $this->userInfo['userId'];
			$this->dbHelper->insertInfo($insertInfo,$this->module_name);
			$this->setResult(true,"Success");
		}
		return $this->result;

	}
	/*
	 * Removing a group
	 */
	public function remove($value) {
		if ($value == '' || !$value) {
			return $this->setResult(false,"No group selected for removal");
		}
		if ($this->checkGroupName($value,false)) {
			$cond['groupName'] = $value;
			$cond['userId'] = $this->userInfo['userId'];
			$this->dbHelper->deleteInfo($cond, $this->module_name);
			$this->setResult(true,"Success");
		}
		return $this->result;
	}
	/*
	 * Updating a keyword list
	 */
	public function update($value) {
		if (!isset($_POST['data'])) {
			return $this->setResult(false,"Update Data Missing");
		}
		if ($this->checkGroupName($value,false)) {
			$set = array();
			$set['keywords'] = strtolower($_POST['data']);
			$keywords = explode("\n",$set['keywords']);
			if (count($keywords) > 10) {
				return $this->setResult(false,"Maximun number of allowed keywords in a Mindspace is 10");
			}
			$condition['groupName'] = $value;
			$condition['userId'] = $this->userInfo['userId'];
			$this->dbHelper->updateInfo($set, $condition, $this->module_name);
			$this->setResult(true,"Success");
		}
		return $this->result;

	}
	/*
	 * get a either the list of groups
	 * or return the keywordList as a new line separated string
	 */
	public function get($value) {
		$selectInfo = array();
		$selectInfo['userId'] = $this->userInfo['userId'];
		if (!$value || $value == '') {
			$res = $this->dbHelper->selectInfo($selectInfo,$this->module_name);
			$this->result->listValues = array();
			for ($idx=0; $idx < count($res); $idx++) {
				$this->result->listValues[] = $res[$idx]['groupName'];
			}
			$this->setResult(true,"Success");
		} else {
			if ($this->checkGroupName($value,false)) {
				$selectInfo['groupName'] = $value;
				$res = $this->dbHelper->selectInfo($selectInfo,$this->module_name);
				if ($res[0]['keywords']) {
					$this->result->value = $res[0]['keywords'];
				} else {
					$this->result->value = '';
				}
				$this->setResult(true,"Success");
			}
		}
		return $this->result;
	}

}

?>