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


$MysqlAffectedRows = 0;
//if(file_exists("queries.sql")) unlink("queries.sql");

/**
* Class to perform mysql database operations
* Database connect parameters are taken from the '$connectDb' config variable declared
* Author: neo@shopon.in
*
/*******************************************************************************************/
class MYSQLHelper
{
    var $host;
    var $port = 3306;
    var $userName;
    var $dbPass;
    var $database;
    var $dbLink;
    var $rowsAffected;
    var $lastInsertId;
    var $errors;
    var $dbError;
    var $arrayIntigerTypes;
    var $arrayFloatTypes;
    var $arrayStringTypes;
    var $arrayDateTypes;

    /**
     * Class Constructor establishes connection to the mysql database
     *
     */
    function  __construct($dbParams = array())
    {
       $this->setFieldTypes();
       $this->connect($dbParams);
       //$this->setTimeZone(date_default_timezone_get());
       return $this;
    }

    /**
     * Sets the field types allowed for each of the data types
     *
     */
    function setFieldTypes()
    {
    	$this->arrayDateTypes    = array("date", "datetime", "timestamp", "time", "year");
    	$this->arrayIntigerTypes = array("int", "tinyint", "smallint", "mediumint", "bigint", "integer", "bool", "boolean");
    	$this->arrayFloatTypes   = array("float", "double", "decimal", "numeric");
    	$this->arrayStringTypes  = array("char", "varchar", "tinytext", "tinyblob", "text", "blob", "mediumtext", "mediumblob" , "longtext", "longblob", "binary", "varbinary", "enum", "set");
    }

    /**
     * Set db connection parameters in to the class values
     * Values can be passed to the function to connect to a different mysql database than the normal one.
     * @param string $host
     * @param integer $port
     * @param string $userName
     * @param string $dbPass
     * @param string $database
     */
    function setDbParams ($params = array())
    {
    	global $connectDb;

        $this->host     = !empty($params['host'])     ? $params['host']     : $connectDb["host"];
        $this->port     = !empty($params['port'])     ? $params['port']     : $connectDb["port"];
        $this->userName = !empty($params['userName']) ? $params['userName'] : $connectDb["user"];
        $this->dbPass   = !empty($params['dbPass'])   ? $params['dbPass']   : $connectDb["pass"];
        $this->database = !empty($params['database']) ? $params['database'] : $connectDb["datb"];
        $this->charSet  = !empty($params['charSet'])  ? $params['charSet']  : $connectDb["charSet"];
    }

    /**
     * Establish connection to the database
     * If the connection parameters are not set in the object this will try to run
     *
     * @return string error on exception
     */
    function connect ($params = array())
    {
    	if(empty($this->host) || empty($this->userName)) {
    		self::setDbParams($params);
    	}

    	// Return error in case on an incomplete configuration
    	if(empty($this->host)) $this->throwError("Mysql Connect Error : Host not specified");
    	if(empty($this->port)) $this->throwError("Mysql Connect Error : Mysql Port number is missing");
    	if(empty($this->userName)) $this->throwError("Mysql Connect Error : No Username");

        try { // Establish connection to the database
            $this->dbLink = mysql_connect("$this->host:$this->port", $this->userName, $this->dbPass);
        } catch (Exception $e) {
            $this->throwError($e);
        }

        if (!$this->dbLink) {
            $this->throwError('Could not connect : Connection to Mysql server failed' . mysql_error());
        }
        //Return if there is an error
        if(!empty($this->errors)) { return false; }

        if(!empty($this->database)) {
        	mysql_select_db($this->database);
        }
        if(!empty($this->charSet)) {
            //echo mysql_client_encoding();
            mysql_set_charset($this->charSet,$this->dbLink);
            //echo "[ -" .mysql_client_encoding();
        }
        return $this;
    }

    /**
     * Closes the mysql connection
     *
     */
    function close ()
    {
        mysql_close($this->dbLink);
    }

    /**
     * Sets the MySQL Time zone
     *
     * @param string $UTCtimeDif
     */
    function setTimeZone ($UTCtimeDif)
    {
        self::queryDb("SET SESSION time_zone = '$UTCtimeDif'");
    }

    function getTableFields ($tblName,$getType = true)/*Function to get the table fields*/
    {
        $result = self::queryDb("DESCRIBE $tblName");

        for ($idx = 0; $idx < $this->rowsAffected; $idx ++) {
            if(!empty($getType)) {
                $fieldInfo["field"][$idx] = $result[$idx]["Field"];
                $fieldInfo["type"][$idx]  = $result[$idx]["Type"];
            } else {
                $fieldInfo[] = $result[$idx]["Field"];
            }
        }
        return $fieldInfo;
    }

    /**
     * Function to verify the data that is to be entered into a database field
     *
     * @param mixed_type $value
     * @param string $type
     * @return boolean
     */
    function verifyVariableType ($value, $type)
    {
        $pass            = false;
        $fieldTypeArray  = explode("(", $type);
        $fieldType       = $fieldTypeArray[0];
        $varType         = gettype($value);

        if (! empty($fieldTypeArray[1])) {
            $fieldLengthArray = explode(")", $fieldTypeArray[1]);
            $fieldLength      = $fieldLengthArray[0];

            if (strstr($fieldLength, ",")) {
                $fieldLengthSubArray = explode(",", $fieldLength);
                $fieldLength         = $fieldLengthSubArray[0];
            }
        }

        if (in_array($fieldType, $this->arrayStringTypes) ||
            in_array($fieldType, $this->arrayIntigerTypes) ||
            in_array($fieldType, $this->arrayFloatTypes)) {
            $pass = $fieldType;
        } else if (in_array($fieldType, $this->arrayDateTypes)) {
            $dateVal = substr($value, 0, 10);

            if (strstr($dateVal, "-")) $parseDate = explode("-", $dateVal);
            if (strstr($dateVal, "/")) $parseDate = explode("/", $dateVal);
                $pass   = checkdate($parseDate[2],$parseDate[1],$parseDate[0]);
        }

        // Check the field length if it is specified in the database
        if (!empty($fieldLength) && strlen($value) > $fieldLength) $pass = false;

        return $pass;
    }

    function queryDb ($query, $assocField = true)
    {
        $result                = mysql_query($query) or die(mysql_error());
        $this->rowsAffected    = $n = mysql_affected_rows();
        $queryType             = substr(ltrim($query), 0, 6);
        $arrayQueryReturnTypes = array("SELECT" , "DESCRI");

        if(!$assocField) {
            while($row = mysql_fetch_array($result, MYSQL_NUM)) {
                $arrayInfo[] = $row;
            }
        } else if (in_array(strtoupper($queryType), $arrayQueryReturnTypes)) {

            $numFields = mysql_num_fields($result);
            $numRrows  = mysql_numrows($result);
            for ($i = 0; $i < $numFields; $i ++) {
                $field           = mysql_fetch_field($result, $i);
                $arrayFields[$i] = self::getVarNameFromFields($field->name);
            }
            $arrayInfo  = "";
            for ($idx1 = 0; $idx1 < $numRrows, $row = mysql_fetch_array($result); $idx1 ++) {

            	for ($idx = 0; $idx < $numFields; $idx ++) {
                    $arrayInfo[$idx1][$arrayFields[$idx]] = $row[$idx];
                }
            }
        } else {
            $arrayInfo = self::lastInsertId();
            $n = 1;
        }

        return (! empty($n) && ! empty($arrayInfo) ? $arrayInfo : null);
    }
    
    function getProperNameValues($arrayInfo,$table) {
    	
    	$fieldInfo      = $this->getTableFields($table);
        $fields         = $fieldInfo["field"];
        $fieldTypes     = $fieldInfo["type"];
        
        $pairs = array();
        
        for ($idx = 0; $idx < count($fields); $idx++) {
            $field      = $fields[$idx];
            $fieldType 	= $fieldTypes[$idx];

            $varName 	= self::getVarNameFromFields($field);
            if (empty($arrayInfo[$varName])) continue;

            $val = mysql_escape_string($arrayInfo[$varName]);
            $varVerify 	= $this->verifyVariableType($val, $fieldType);

            if (!$varVerify) {
                if (! empty($this->dbError)) {
                    $this->dbError .= " incorrect data for $field($fieldType) -< $val <br />";
                } else {
                    $this->dbError = "There is some error in the data that you have enterd. Your data could not be processed. error message-> incorrect data for $field($fieldType) -< $varName <br />";
                }
                continue;
            }

            $fieldType 			= $varVerify;
            if (in_array($fieldType, $this->arrayIntigerTypes)) {
            	$val = intval($val);
            }
            if (in_array($fieldType, $this->arrayFloatTypes)) {
            	$val = doubleval($val);
            }
         	if (in_array($fieldType, $this->arrayStringTypes)) {
         	    if ($field == "password") {
              	  	$val =  "\"" . md5($val). "\"";
            	} else {
            		$val = "\"".$val."\"";
            	}
            }
            $pair = new stdClass;
            $pair->name = $field;
            $pair->value =  $val;
            $pairs[] = $pair;
          
        }
        return $pairs;
    }
    
    function selectInfo($arrayInfo,$table)
    {
    	$pairs = self::getProperNameValues($arrayInfo,$table);
    	$condition = "";
         for ($idx = 0; $idx < count($pairs); $idx++) {
        	$condition .= $pairs[$idx]->name . "=" . $pairs[$idx]->value;
            $condition .= " AND "; 
          
        }
        $condition .= " TRUE";
    	$query = "SELECT * FROM ".$table." Where ".$condition;
    	return self::queryDB($query);
    }

    function insertInfo ($arrayInfo, $table, $idField = null, $id = null)
    {
    	$pairs = self::getProperNameValues($arrayInfo,$table);
        $fieldsToInsert = "";
        $valuesToInsert = "";
        for ($idx = 0; $idx < count($pairs); $idx ++) {
            $fieldsToInsert .= $pairs[$idx]->name. ",";
            $valuesToInsert .=  $pairs[$idx]->value.",";
        }
        $fieldsToInsert = substr($fieldsToInsert, 0, strlen($fieldsToInsert) - 1);
        $valuesToInsert = substr($valuesToInsert, 0, strlen($valuesToInsert) - 1);
        $query = "INSERT INTO $table ($fieldsToInsert) VALUES($valuesToInsert)";
        if (empty($this->dbError)) {
            self::queryDb($query);
        } else {
        	print_r($this->dbError);
        }
        return null;
    }

    function updateInfo ($arrayInfo, $where , $table)
    {
        $pairs = self::getProperNameValues($arrayInfo,$table);
        $query    = "";

        for ($idx = 0; $idx < count($pairs); $idx ++) {
          $query .= $pairs[$idx]->name . "=" . $pairs[$idx]->value . ",";
        }
        $query = substr($query, 0, strlen($query) - 1);
       
        $pairs = self::getProperNameValues($where,$table);
        $condition = "";
    	
        for ($idx = 0; $idx < count($pairs); $idx ++) {
          $condition .= $pairs[$idx]->name . "=" . $pairs[$idx]->value . " AND ";
        }
        $condition .= " TRUE";
        
        $query       = "UPDATE $table SET $query WHERE $condition";
        if (empty($this->dbError)) {
            self::queryDb($query);
        }
    }
    
    function deleteInfo($delInfo,$table) {
    	$pairs = self::getProperNameValues($delInfo,$table);
        $condition    = "";
        for ($idx = 0; $idx < count($pairs); $idx ++) {
          $condition .= $pairs[$idx]->name . "=" . $pairs[$idx]->value . " AND ";
        }
        $condition .= " TRUE";
        $query = "DELETE FROM ".$table." WHERE ".$condition;
    	if (empty($this->dbError)) {
            self::queryDb($query);
        }
    }
  
    function getVarNameFromFields ($fieldName)/*Function to create variable name in the camel structure from the fields*/
	{
        $strArray = explode("_", $fieldName);
        $varName  = $strArray[0];
        for ($idx = 1; $idx < count($strArray); $idx ++) {
            $varName .= ucfirst($strArray[$idx]);
        }

        return $varName;
    }

    function lastInsertId()
    {
        $lastInsertId = self::queryDb("SELECT LAST_INSERT_ID() AS last_id;");
        $this->lastInsertId = $lastInsertId[0]["lastId"];
        return ($this->lastInsertId);
    }

    private function throwError($str)
    {
        $this->errors[] = $str;
        return true;
    }

    public function getErrors()
    {
        if(!empty($this->errors)) {
            return $this->errors;
        }
        return false;
    }
}
/*
 * Global Object
 */

$gDBHelper = new MYSQLHelper();
