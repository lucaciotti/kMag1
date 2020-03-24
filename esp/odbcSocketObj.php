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
		$Error="";              // Last generated error message
		$Recordset=null;        // An array containing the recordset just in case you want to deal with it directly
		$BOF = true;            // Indicates that the current record position is before the first record in a Recordset object.
		$EOF = true;            // Indicates that the current record position is after the last record in a Recordset object.
		$RecordCount=0;         // The number of records returned
		$FieldNames;            // The names of the fields
		$FieldTypes;			// The types of the fields
		$FieldCount=0;          // The number of fields returned
		$_currentRow    		// This variable keeps the current row in the Recordset.

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

	/* Private variables
    These are all read only please don't mess with them directly
    */
    private $HostName;
    private $Port = 9628;
    private $ConnectionString;
    private $CaseFolding = true;
    private $XML = "";

    private $Error = "";
    private $Recordset = null;
    public $BOF = true;
    public $EOF = true;
    private $RecordCount = 0;
    private $FieldNames;
    private $FieldTypes;
    private $FieldCount = 0;

    private $_currentRow = - 1;

    // This variable keeps the current row in the Recordset.
    private $_result = true;
    private $_parser;
    private $_currentTag;

    ///// GETTER

    public function getHostName() {
        return $this->HostName;
    }

    public function getPort() {
        return $this->Port;
    }

    public function getConnectionString() {
        return $this->ConnectionString;
    }

    public function getCurrentRow() {
		return $this->_currentRow;
	}

	public function getFieldTypes($field) {
		$field = strtolower($field);
		return $this->FieldTypes[$field];
	}

	public function getFieldNames() {
		return $this->FieldNames;
	}

	public function cloneFieldTypes() {
		return $this->FieldTypes;
	}

	public function cloneRecordSet() {
		return $this->Recordset;
	}

	public function cloneFieldNames() {
		return $this->FieldNames;
	}

	public function getNumOfRows() {
		return $this->RecordCount;
	}

	public function getNumOfFields() {
		return $this->FieldCount;
	}

	public function getField($field) {
		// This is so that you can get a field using mixed case
		// If you don't have casefoldinng on and your field names are mixed case
		// this will be very useful.

		if ($this->EOF || $this->BOF) return;

		$field=strtoupper($field);

		if ($this->CaseFolding){
			// a shortcut for case folding
			if (strcasecmp($this->getFieldTypes($field), 'boolean') == 0){
				return ($this->Recordset[$this->_currentRow][$field] == 'True') ? 1 : 0;
			}
			else if (strcasecmp($this->getFieldTypes($field), 'float') == 0){
				return floatval($this->Recordset[$this->_currentRow][$field]);
			}
			else{
				return $this->Recordset[$this->_currentRow][$field];
			}
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
				}
			}
		}
	}

	///// SETTER

    public function _setHostName($HostName) {
        $this->HostName = $HostName;

        return $this;
    }


    public function _setPort($Port) {
        $this->Port = $Port;

        return $this;
    }

    public function _setConnectionString($ConnectionString) {
        $this->ConnectionString = $ConnectionString;

        return $this;
    }

    private function _setFieldTypes($type){
		$i = count($this->FieldNames) -1;
		$field = $this->FieldNames[$i];
		$this->FieldTypes[$field]= $type;
	}

	public function errorMsg() {
		return $this->Error;
	}

	public function xmlMsg() {
		return $this->XML;
	}


	/// MOVE BETWEEN RESULT AND LINE

	public function nextRow(){
		$this->_currentRow++ ;
		$this->_setCurrentRecord();
	}

	public function moveNext(){
		$this->nextRow();
	}

	public function prevRow(){
		$this->_currentRow-- ;
		$this->_setCurrentRecord();
	}

	public function movePrev(){
		$this->prevRow();
	}

	public function firstRow() {
		$this->_currentRow = 0 ;
		$this->_setCurrentRecord();
	}

	public function moveFirst() {
		$this->firstRow();
	}

	public function lastRow() {
		$this->_currentRow= $this->Recordcount -1 ;
		$this->_setCurrentRecord();
	}

	public function moveLast() {
		$this->lastRow();
	}

	/// CODE SOCKET & QUERY

    public function execute($SQL) {

        // This function attempts to execute an SQL statement and returns true or false.
        // If it returns false check the $Error variable to see what went wrong.

        // First reset current objects
        $this->XML = "";
        $this->Error = "";
        $this->Recordset = null;

        //Replace disEqual statement for FoxProDB
        $SQL = str_replace("!=", "<>", $SQL);

        // This will set $this->XML if a succesful
        // connection and exchange takes place.
        if (!$this->_sendSQL($SQL)) return false;

        // parse the resultant XML This will set the
        // $this->_result variable
        $this->_parseXML();

        $this->_initRecordset();

        return $this->_result;
    }

    private function _sendSQL($sSQL) {

        $socket = fsockopen($this->HostName, $this->Port, $errno, $errstr, 180);
        // we are going to deal with connection errors here
        if (!$socket) {

            //contruct ErrorString string to return
            $this->XML = "<?xml version=\"1.0\"?>\r\n<result state=\"failure\">\r\n<ErrorString>$errstr</ErrorString>\r\n</result>\r\n";

            //$this->ErrNo=$errno;
            $this->Error = $errstr;
            $this->_result = false;
            $retval = false;
        } else {
        	//construct XML to send
            //search and replace HTML chars in SQL first
            $sSQL = HTMLSpecialChars($sSQL);
            $sSend = "<?xml version=\"1.0\"?>\r\n<request>\r\n<connectionstring>$this->ConnectionString</connectionstring>\r\n<sql>$sSQL</sql>\r\n</request>\r\n";

            //write request
            fputs($socket, $sSend);

            $result = "";

            //now read response
            while (!feof($socket)) {
                $result = $result.fgets($socket, 128);
            };

            fclose($socket);

            $this->XML = $result;

            $retval = true;
        }
        return retval;
    }

    private function _parseXML() {

        $this->_parser = xml_parser_create();
        xml_set_element_handler($this->_parser, "_startElement", "_endElement");
        xml_set_character_data_handler($this->_parser, "_characterData");
        xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, $this->CaseFolding);

        //xml_set_object($this->_parser,&$this);
        xml_set_object($this->_parser, $this);

        // We are going to presume everything will be OK
        // If there are any problems then this will be reset.
        // by one of the callback functions
        $this->_result = true;

        if (!xml_parse($this->_parser, $this->XML)) {
            $this->_result = false;
            $this->Error = sprintf("XML ErrorString: %s at line %d", xml_ErrorString_string(xml_get_ErrorString_code($this->_parser)), xml_get_current_line_number($this->_parser));
        }

        xml_parser_free($this->_parser);
    }
    /* XML related functions.. */
    private function _startElement($parser, $name, $attribs)	{

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
	private function _endElement($parser, $name)	{
		// not used here
		return;
	}
	//handler for character data
	private function _characterData($parser, $data)	{
		if (($this->_currentTag == "error") && strlen(trim($data)) != 0 )
			$this->Error = $data;
	}

	/// Recordset Related functions
	private function _initRecordset() {
		$this->RecordCount = count($this->Recordset);
		$this->FieldCount = count($this->FieldNames);
		$this->_currentRow = 0;
		$this->_setCurrentRecord();
	}

	private function _setCurrentRecord() {
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
        	}
		}
	}

}

?>
