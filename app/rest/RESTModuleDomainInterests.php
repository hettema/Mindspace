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


class RESTModuleDomainInterests extends RESTModule{

	
	private function checkCategoryName($categoryName,$ifExistsThenError) {
		if ($categoryName == '') {
			$this->setResult(false,"Please select a  category");
			return false;
		}
		if (false) {
			return $this->setResult(false,"Invalid Category Name; please use A-Z or a-z or _ or  0-9 for creating group names");
		}
		$selectInfo = array();
		$selectInfo['categoryName'] = $categoryName;
		$selectInfo['userId'] = $this->userInfo['userId'];
		$res = $this->dbHelper->selectInfo($selectInfo,$this->module_name);
		$res = $res[0];
		
		if ($ifExistsThenError) {
			if (isset($res['categoryName'])) {
				$this->setResult(false,"Category Name Already present");
				return false;
			}
		} else {
			/*If doesn't exist - then error; */
			if (!isset($res['categoryName'])) {
				$this->setResult(false,"Invalid Group Name");
				return false;
			}
		}
		return true;
		
	}
	public function add($value) {
		if ($this->checkCategoryName($value,true)) {
			$inserInfo = array();
			$insertInfo['categoryName'] = $value;
			$insertInfo['domains'] = '';
			$insertInfo['userId'] = $this->userInfo['userId'];
			$this->dbHelper->insertInfo($insertInfo,$this->module_name);
			$this->setResult(true,"Success");
		}
		return $this->result; 
		
	}
	
	public function remove($value) {
		if ($value == '' || !$value) {
			return $this->setResult(false,"Please Select a category to remove");
		}
		if ($this->checkCategoryName($value,false)) {
			$cond['categoryName'] = $value;
			$cond['userId'] = $this->userInfo['userId'];
			$this->dbHelper->deleteInfo($cond, $this->module_name);
			$this->setResult(true,"Success");
		}
		return $this->result;
	}
	public function update($value) {
		if (!isset($_POST['data'])) {
			return $this->setResult(false,"Update Data Missing");
		}
		if ($this->checkCategoryName($value,false)) {
			$set = array();
			$set['domains'] = $_POST['data'];
			$condition['categoryName'] = $value;
			$condition['userId'] = $this->userInfo['userId'];
			$this->dbHelper->updateInfo($set, $condition, $this->module_name);
			$this->setResult(true,"Success");
		} 
		return $this->result;
		
	}
	public function get($value) {
		$selectInfo = array();
		$selectInfo['userId'] = $this->userInfo['userId'];
		if (!$value || $value == '') {
			$res = $this->dbHelper->selectInfo($selectInfo,$this->module_name);
			$this->result->listValues = array();
			for ($idx=0; $idx < count($res); $idx++) {
				$this->result->listValues[] = $res[$idx]['categoryName'];
			}
			$this->setResult(true,"Success");
		} else {
			if ($this->checkCategoryName($value,false)) {
				$selectInfo['categoryName'] = $value;
				$res = $this->dbHelper->selectInfo($selectInfo,$this->module_name);
				if ($res[0]['domains']) {
					$this->result->value = $res[0]['domains'];
				} else {
					$this->result->value = '';
				}
				$this->setResult(true,"Success");
			}
		}
		return $this->result;
	}
	
};

?>