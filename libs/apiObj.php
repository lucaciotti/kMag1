<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/kMag1/libs/arcaRestAPI.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/kMag1/libs/utilsFunctions.php');

class apiObj{
    private static $initialized;
    private static $conn;
    private $JSON = null;
    private $CaseFolding = false;

    private $Error = "";
    private $Recordset = null;
    public $BOF = true;
    public $EOF = true;
    private $RecordCount = 0;
    private $FieldNames;
    private $FieldTypes;
    private $FieldCount = 0;

    private $_currentRow = -1;
    private $_result = true;

    /// CODE SOCKET & QUERY
    public function execute($SQL)
    {
        // This function attempts to execute an SQL statement and returns true or false.
        // If it returns false check the $Error variable to see what went wrong.

        // First reset current objects
        $this->XML = "";
        $this->Error = "";
        $this->Recordset = null;

        //Replace disEqual statement for FoxProDB
        $SQL = str_replace("!=", "<>", $SQL);
        // $SQL = HTMLSpecialChars($SQL);

        // This will set $this->XML if a succesful
        // connection and exchange takes place.
        if (!$this->_sendSQL($SQL)) return false;

        $this->_initRecordset();

        return $this->_result;
    }

    private function _sendSQL($query){
        if (!self::$initialized){
            self::$conn = new arcaRestAPI();
            self::$initialized = true;
        }
        if (!self::$conn) {
            $this->Error = 'Rest Api Connection Problem';
            $this->_result = false;
            return false;
        }

        $res = null;
        $data = array(
            "query" => $query,
        );
        
        if (UtilsFunctions::startsWith($query, 'SELECT')) {
            $res = self::$conn->post('selectQuery', $data);
            if(empty($res->error)){
                $this->Recordset=$res['data'];
                $this->FieldNames = array_keys($res['data'][0]);
                $this->Error= $res['error'];
                $this->JSON=$res['json'];
                $this->_result = true;
            } else {
                $this->Error = $res['error'];
                $this->JSON = $res['json'];
                $this->_result = false;
            }
        } else {
            if (UtilsFunctions::startsWith($query, 'INSERT')) $res = self::$conn->put('insertQuery', $data);
            if (UtilsFunctions::startsWith($query, 'UPDATE')) $res = self::$conn->put('updateQuery', $data);
            if (UtilsFunctions::startsWith($query, 'DELETE')) $res = self::$conn->delete('deleteQuery', $data);

            if($res=='success') {
                $this->_result = true;
            } else {
                $this->Error = $res['error'];
                $this->JSON = $res['json'];
                $this->_result = false;
            }
        }
        return $this->_result;
    }


    /// Recordset Related functions
    private function _initRecordset() {
        $this->RecordCount = count($this->Recordset);
        $this->FieldCount = count($this->FieldNames);
        $this->_currentRow = 0;
        $this->_setCurrentRecord();
        if($this->FieldCount>0){
            $firstSet = false;
            $type='';
            for ($i=0; $i < $this->FieldCount ; $i++) {
                if(preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}([.]\d{1,3})?(Z|[-+]\d{2}:\d{2})/', $this->Recordset[0][$this->FieldNames[$i]])){
                    $type = 'dateTime';
                } else{
                    $type = gettype($this->Recordset[0][$this->FieldNames[$i]]);
                }
                if(!$firstSet){
                    $firstSet=true;
                    $this->FieldTypes = array($this->FieldNames[$i] => $type);
                } else {
                    $this->FieldTypes = array_merge($this->FieldTypes, array($this->FieldNames[$i] => $type));
                }
            }
        }
    }

    private function _setCurrentRecord() {
        if (0 == $this->RecordCount) {
            $this->EOF = true;
            $this->BOF = true;
        } else if ($this->_currentRow > ($this->RecordCount - 1)) {
            $this->EOF = true;
            $this->BOF = false;
            $this->_currentRow = -1;
        } else if ($this->_currentRow < 0) {
            $this->BOF = true;
            $this->EOF = false;
            $this->_currentRow = -1;
        } else {
            $this->EOF = false;
            $this->BOF = false;
            $record = $this->Recordset[$this->_currentRow];
            while (list($key, $value) = each($record)) {
                $this->$key = $value;
            }
        }
    }

    // GETTER PROPERTIES
    public function getField($field) {
        if ($this->EOF || $this->BOF) return;
        $field = strtoupper($field);
        if ($this->CaseFolding) {
            // a shortcut for case folding
            if (strcasecmp($this->getFieldTypes($field), 'boolean') == 0) {
                return ($this->Recordset[$this->_currentRow][$field] == 'True') ? 1 : 0;
            } else if (strcasecmp($this->getFieldTypes($field), 'float') == 0) {
                return floatval($this->Recordset[$this->_currentRow][$field]);
            } else if (strcasecmp($this->getFieldTypes($field), 'dateTime') == 0) {
                return date("d-m-Y", strtotime($this->Recordset[$this->_currentRow][$field]));
            } else {
                return $this->Recordset[$this->_currentRow][$field];
            }
        } else {
            foreach ($this->FieldNames as $fieldname) {
                if (strtoupper($fieldname) == $field) {
                    if ($this->getFieldTypes($fieldname) == 'boolean') {
                        return ($this->Recordset[$this->_currentRow][$fieldname] == 'True') ? 1 : 0;
                    } else if ($this->getFieldTypes($fieldname) == 'float') {
                        return floatval($this->Recordset[$this->_currentRow][$fieldname]);
                    } else if (strcasecmp($this->getFieldTypes($field), 'dateTime') == 0) {
                        return date("d-m-Y", strtotime($this->Recordset[$this->_currentRow][$fieldname]));
                    } else {
                        return $this->Recordset[$this->_currentRow][$fieldname];
                    }
                }
            }
        }
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

    public function errorMsg() {
        return $this->Error;
    }

    public function jsonMsg() {
        return $this->JSON;
    }

    /// MOVE BETWEEN RESULT AND LINE
    public function nextRow(){
        $this->_currentRow++;
        $this->_setCurrentRecord();
    }

    public function moveNext(){
        $this->nextRow();
    }

    public function prevRow(){
        $this->_currentRow--;
        $this->_setCurrentRecord();
    }

    public function movePrev(){
        $this->prevRow();
    }

    public function firstRow(){
        $this->_currentRow = 0;
        $this->_setCurrentRecord();
    }

    public function moveFirst(){
        $this->firstRow();
    }

    public function lastRow(){
        $this->_currentRow = $this->Recordcount - 1;
        $this->_setCurrentRecord();
    }

    public function moveLast(){
        $this->lastRow();
    }

}