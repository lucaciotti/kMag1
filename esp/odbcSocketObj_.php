<?php

/*
	Name: OdbcSocketServer PHP Object
	Version: .52.01
	Description: A PHP  wrapper the ODBC Socket Server  with a
	Recordset for result data manipulation.
	First Author: Tim Uckun
	Second Author: Luca Ciotti
	Last Modified Date: 09/25/2013

	Copyright (c) Tim Uckun & Luca Ciotti
	All rights reserved.

	This Software is distributed under the GPL. For a list of your obligations and rights
	under this license please visit the GNU website at http://www.gnu.org/
*/

/* Interface.

	public variables:
		$HostName;              // Name of the host to connect to
		$Port=9628;             // Port to connect to (set to the default)
		$ConnectionString;      // Connection string to use I will show an example later.
		$CaseFolding=true;      // Determines weather or not all tags are mapped to uppercase pretty useful sometimes
		$XML="";                // the raw XML as returned from the socket server
		$Error="";              // Please don't use use the function instead
		$Recordset=null;        // An array containing the recordset just in case you want to deal with it directly
		$BOF = true;            // Indicates that the current record position is before the first record in a Recordset object.
		$EOF = true;            // Indicates that the current record position is after the last record in a Recordset object.
		$RecordCount=0;         // Please don't use use the function instead
		$FieldNames;            // Please don't use use the function instead
		$FieldCount=0;          // Please don't use use the function instead

	Public Functions

		execute($SQL)
		errorMsg()
		getField($field)


		moveNext()
		nextRow()

		movePrev()
		prevRow()

		moveFirst()
		firstRow()

		moveLast()
		lastRow()

		moveRow($rowNumber = 0)
		getNumOfRows()
		getNumOfFields()
		getFieldNames()

*/

 class ODBCSocketServer {

	/* Public variables */
	var $HostName;              // Name of the host to connect to
	var $Port=9628;             // Port to connect to (set to the default)
	var $ConnectionString;      // Connection string to use I will show an example later.
	var $CaseFolding=true;      // Determines weather or not all tags are mapped to uppercase pretty useful sometimes
	var $XML="";                // the raw XML as returned from the socket server
	var $Error="";                 // Last generated error message
	var $Recordset=null;        // An array containing the recordset just in case you want to deal with it directly
	var $BOF = true;           // Indicates that the current record position is before the first record in a Recordset object.
	var $EOF = true;           // Indicates that the current record position is after the last record in a Recordset object.
	var $RecordCount=0;         // The number of records returned
	var $FieldNames;            // The names of the fields
	var $FieldTypes;            // The types of the fields
	var $FieldCount=0;          // The number of fields returned

	/*	private variables	*/
	// These are all read only please don't mess with them directly

	var $_currentRow = -1;	/*	This variable keeps the current row in the Recordset.	*/
	var $_result=true;
 	var $_parser;
	var $_currentTag;

	/*TODO  COSTRUTTORE DA IMPLEMENTARE
	function __construct($cHost, $cConnString){
		$this->Hostname = (string) $cHost;
		$this->ConnectionString = (string) $cConnString;
	}*/

	//function to parse the SQL

	function execute($SQL) {

		// This function attempts to execute an SQL statement and returns true or false.
		// If it returns false check the $Error variable to see what went wrong.

		// First reset current objects

		$this->XML="";
		$this->Error="";
		$this->Recordset=null;

		$SQL = str_replace("!=", "<>", $SQL);
		// This will set $this->XML if a succesful
		// connection and exchange takes place.
		if (!$this->_sendSQL($SQL)) return false;

		// parse the resultant XML This will set the
		// $this->_result variable

		$this->_parseXML();

		$this->_initRecordset();

		return $this->_result;
	} //execSql

	/*TODO  Finire Implementazione
	function Update($SQL) {

		// This function attempts to execute an SQL statement and returns true or false.
		// If it returns false check the $Error variable to see what went wrong.

		// First reset current objects

		$this->XML="";
		$this->Error="";
		$this->Recordset=null;

		// This will set $this->XML if a succesful
		// connection and exchange takes place.

		if (!$this->_sendSQL($SQL)) return false;

		$this->_parseXML();

		$this->_initRecordset();

		return $this->_result;
	} //execSql For Updating and Inserting only*/

	function errorMsg() {
		return $this->Error;
	}

	function xmlMsg() {
		return $this->XML;
	}

	function getField($field) {
		// This is so that you can get a field using mixed case
		// If you don't have casefoldinng on and your field names are mixed case
		// this will be very useful.

		if ($this->EOF || $this->BOF) return;

		$field=strtoupper($field);

		if ($this->CaseFolding){
			// a shortcut for case folding
			//if ($this->getFieldTypes($field) == 'boolean'){
			if (strcasecmp($this->getFieldTypes($field), 'boolean') == 0){
				return ($this->Recordset[$this->_currentRow][$field] == 'True') ? 1 : 0;
			}
			else if (strcasecmp($this->getFieldTypes($field), 'float') == 0){
				return floatval($this->Recordset[$this->_currentRow][$field]);
			}
			else{
				return $this->Recordset[$this->_currentRow][$field];
			}
			//return $this->Recordset[$this->_currentRow][$field];
		}else{

			foreach ($this->FieldNames as $fieldname) {
				if (strtoupper($fieldname) == $field){
					if ($this->getFieldTypes($fieldname) == 'boolean'){
						return ($this->Recordset[$this->_currentRow][$fieldname] == 'True') ? 1 : 0;
					}
					else if ($this->getFieldTypes($fieldname) == 'float'){
						return floatval($this->Recordset[$this->_currentRow][$fieldname]);
					}
					else{
						return $this->Recordset[$this->_currentRow][$fieldname];
					}
					//return $this->Recordset[$this->_currentRow][$fieldname];
				} // if
			} // foreach
		} // else

	}// getfield

	function nextRow(){
		$this->_currentRow++ ;
		$this->_setCurrentRecord();
	}

	function  moveNext(){
		$this->nextRow();
	}

	function prevRow(){
		$this->_currentRow-- ;
		$this->_setCurrentRecord();
	}

	function movePrev(){
		$this->prevRow();
	}

	function firstRow() {
		$this->_currentRow = 0 ;
		$this->_setCurrentRecord();
	}

	function moveFirst() {
		$this->firstRow();
	}

	function lastRow() {
		$this->_currentRow= $this->Recordcount -1 ;
		$this->_setCurrentRecord();
	}

	function moveLast() {
		$this->lastRow();
	}

	function moveRow($rowNumber = 0) {
		$this->_currentRow=$rowNumber;
		$this->_setCurrentRecord();
	}

	function getCurrentRow() {
		return $this->_currentRow;
	}

	function getNumOfRows() {
		return $this->RecordCount;
	}

	function getNumOfFields() {
		return $this->FieldCount;
	}

	function getFieldNames() {
		return $this->FieldNames;
	}

	function getFieldTypes($field) {
		$field = strtolower($field);
		return $this->FieldTypes[$field];
	}

	//handler for character data
	function _characterData($parser, $data)
	{
		if (($this->_currentTag == "error") && strlen(trim($data)) != 0 )
			$this->Error = $data;
	}

	function _parseXML(){

		$this->_parser = xml_parser_create();
		xml_set_element_handler($this->_parser, "_startElement", "_endElement");
       	xml_set_character_data_handler($this->_parser, "_characterData");
		xml_parser_set_option ($this->_parser,XML_OPTION_CASE_FOLDING,$this->CaseFolding);
        //xml_set_object($this->_parser,&$this);
        xml_set_object($this->_parser,$this);

			// We are going to presume everything will be OK
			// If there are any problems then this will be reset.
			// by one of the callback functions

		$this->_result = true;

        if (!xml_parse($this->_parser, $this->XML)) {

			$this->_result=false;
			$this->Error=sprintf("XML ErrorString: %s at line %d",
						xml_ErrorString_string(xml_get_ErrorString_code($this->_parser)),
                  		xml_get_current_line_number($this->_parser));

		}
		xml_parser_free($this->_parser);
	}

	function _sendSQL($sSQL){


        $socket = fsockopen($this->HostName, $this->Port, $errno, $errstr, 180);


		// $fToOpen = fsockopen($this->HostName, $this->Port, $errno, $errstr, 30);

		// we are going to deal with connection errors here

		if (!$socket){

			//contruct ErrorString string to return
			$this->XML = "<?xml version=\"1.0\"?>\r\n<result state=\"failure\">\r\n<ErrorString>$errstr</ErrorString>\r\n</result>\r\n";
			//$this->ErrNo=$errno;
			$this->Error=$errstr;
			$this->_result=false;
			$retval=false;
		}else{

			//construct XML to send
			//search and replace HTML chars in SQL first
			$sSQL = HTMLSpecialChars($sSQL);
			$sSend = "<?xml version=\"1.0\"?>\r\n<request>\r\n<connectionstring>$this->ConnectionString</connectionstring>\r\n<sql>$sSQL</sql>\r\n</request>\r\n";

			//write request

			fputs($socket, $sSend);

			$result = "";

			//now read response
			while (!feof($socket))	{

				$result= $result . fgets($socket, 128);
			} // while


			fclose($socket);

			$this->XML=$result;

			$retval=true;
		} // if

		return $retval;
	} // _sendSQL

	function _setFieldTypes($type){

		$i = count($this->FieldNames) -1;
		$field = $this->FieldNames[$i];
		$this->FieldTypes[$field]= $type;
	}

	/* XML related functions.. */

	function _startElement($parser, $name, $attribs)	{

		$this->_currentTag = strtolower($name);
		switch ($this->_currentTag){
			case "s:attributetype":

				if ($this->CaseFolding)
					$fieldval=$attribs["NAME"];
				else
					$fieldval=$attribs["name"];

				$this->FieldNames[]=$fieldval;
				break;

			case "s:datatype":

				if ($this->CaseFolding)
					$fieldtype=$attribs["DT:TYPE"];
				else
					$fieldtype=$attribs["dt:type"];

				$this->_setFieldTypes($fieldtype);
				//$this->FieldTypes[]=$fieldtype;
				break;

			case "z:row":

				$this->Recordset[]=$attribs;
				break;

			case "result":

				if ($this->CaseFolding)
					$fieldval=$attribs["STATE"];
				else
					$fieldval=$attribs["state"];


				if ($fieldval == "success")
					$this->_result=1;
				else
					$this->_result=0;

				break;
			case "error":
				// this is text so just set  the _result
				$this->_result=0;
				break;
		}
	}

	//handler for the end of elements
	function _endElement($parser, $name)
	{
		// not used here
		return;
	}

	/// Recordset Related functions
	function _initRecordset() {

		$this->RecordCount = count($this->Recordset);
		$this->FieldCount=count($this->FieldNames);
		$this->_currentRow = 0;
		$this->_setCurrentRecord();

	} // _initRecordset()

	function _setCurrentRecord()	{

		if (0 == $this->RecordCount){
			$this->EOF = true;
			$this->BOF = true;
		}else if ($this->_currentRow > ($this->RecordCount - 1)){
			$this->EOF=true;
			$this->BOF = false;
			$this->_currentRow = -1;
		}else if ($this->_currentRow < 0){
			$this->BOF=true;
			$this->EOF=false;
			$this->_currentRow = -1;
		}else{
			$this->EOF = false;
			$this->BOF = false;
			$record=$this->Recordset[$this->_currentRow];
			while (list($key, $value) = each ($record)) {
         		$this->$key = $value;
        	}// while
		} // if
	} // _setCurrentRecord

}//class

?>
