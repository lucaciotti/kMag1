<?php

/*
private $id_testa;
    private $id_riga;
    private $codArt;
    private $descrArt;
    private $qta;
    private $qtaRes;
    private $um;
    private $lotto;
    private $gruppo;
    private $numRiga;
    private $reparto;
    private $collo;
    private $bancale;
    private $idRigaOrd;
    private $idTesOrd;
    private $idRigaOrig;
    private $idTesOrig;
*/

class PackingList
{
    private $errorMsg;
    private $CaseFolding = false;

    private $Recordset = null;
    private $FieldNames = null;
    private $FieldTypes = null;
    public $BOF = true;
    public $EOF = true;
    private $RecordCount = 0;
    private $FieldCount = 0;

    private $_currentRow = -1;

    public function findByNumAnno($num, $anno, $db)
    {
        $db1 = clone $db;
        $Query = "SELECT doctes.id FROM doctes WHERE doctes.tipodoc='PL' and doctes.esercizio='$anno' and alltrim(doctes.numerodoc)='$num'";

        if (!$db->Execute($Query)) {
            $this->errorMsg = $db->errorMsg();
            $db = null;

            return -2;
        }

        return $this->findByIdTesta($db->getField('id'), $db1); //, "docrig.codicearti = '0910 9'"
    }

    public function findByIdTesta($id, $db, $where = null)
    {
        $Query = "SELECT ";
        $Query .= "doctes.id as id_testa, doctes.numerodoc, doctes.datadoc, doctes.codicecf, doctes.vettore1, ";
        $Query .= "anagrafe.descrizion  as ragSoc ";
        $Query .= 'FROM ';
        $Query .= 'doctes ';
        $Query .= 'inner join anagrafe on anagrafe.codice = doctes.codicecf ';
        $Query .= 'WHERE ';
        $Query .= "doctes.id = $id";
        //magart.fatt3, ";
        //$Query.= "anagrafe.descrizion  as ragSoc ";
        //$Query.= "inner join anagrafe on anagrafe.codice = docrig.codicecf ";

        if (!is_null($where)) {
            $Query .= ' AND '.$where;
        }

        if (!$db->Execute($Query)) {
            $this->errorMsg = $db->errorMsg().$Query;
            $db = null;

            return -1;
        } else {
            $this->errorMsg = $Query;
        }

        $this->Recordset = $db->cloneRecordSet();
        $this->FieldNames = $db->cloneFieldNames();
        $this->FieldTypes = $db->cloneFieldTypes();
        $this->RecordCount = $db->getNumOfRows();
        $this->FieldCount = $db->getNumOfFields();

        $db = null;
        $this->_setCurrentRecord();

        return $this->RecordCount;
    }

    public function findByRowPL($id, $db, $where = null)
    {
        $Query = "SELECT ";
        $Query .= "docrig.id as id_riga, docrig.codicearti, docrig.descrizion, docrig.quantita, docrig.quantitare, ";
        $Query .= "docrig.unmisura, docrig.lotto, docrig.gruppo, docrig.numeroriga, docrig.rifcer as reparto, ";
        $Query .= "docrig.riffromr as idRigaOrd, docrig.riffromt as idTesOrd, ";
        $Query .= "magart.unmisura as umPrArt, magart.unmisura1, magart.unmisura2, magart.unmisura3, magart.fatt1, magart.fatt2, magart.fatt3, docrig.prezzoacq, ";
        $Query .= "ordtes.numerodoc as numOrd ";
        $Query .= "FROM ";
        $Query .= "docrig left join magart on magart.codice = docrig.codicearti ";
        $Query .= "left join doctes ordtes on ordtes.id = docrig.riffromt ";
        $Query .= "WHERE ";
        $Query .= "docrig.id_testa = $id ";
        //AND docrig.quantitare > 0

        if (!is_null($where)) {
            $Query .= ' AND '.$where;
        }

        if (!$db->Execute($Query)) {
            $this->errorMsg = $db->errorMsg().$Query;
            $db = null;

            return -1;
        } else {
            $this->errorMsg = $db->errorMsg().$Query;
        }

        $this->Recordset = $db->cloneRecordSet();
        $this->FieldNames = $db->cloneFieldNames();
        $this->FieldTypes = $db->cloneFieldTypes();
        $this->RecordCount = $db->getNumOfRows();
        $this->FieldCount = $db->getNumOfFields();

        $db = null;
        $this->_setCurrentRecord();

        return $this->RecordCount;
    }

    public function findByRowPB($id, $db, $where = null)
    {
        $Query = "SELECT ";
        $Query .= "docrig.id as id_riga, docrig.id_testa, docrig.codicearti, docrig.descrizion, docrig.quantita, docrig.quantitare, ";
        $Query .= "docrig.unmisura, docrig.lotto, docrig.gruppo, docrig.numeroriga, docrig.rifcer as reparto, docrig.u_costk as collo, docrig.u_costk1 as bancale, ";
        $Query .= "docrig.riffromr as idRigaPL, docrig.riffromt as idTesPL, docrig.rifstato, magart.fatt3, ";
        $Query .= "magart.danger as imballo, magart.pesounit as peso, magart.unmisura as umPrArt, magart.unmisura1, magart.unmisura2, magart.unmisura3, magart.fatt1, magart.fatt2, docrig.prezzoacq ";
        $Query .= "FROM ";
        $Query .= "docrig left join magart on magart.codice = docrig.codicearti ";
        $Query .= "WHERE ";
        $Query .= "docrig.riffromt = $id";

        if (!is_null($where)) {
            $Query .= ' AND '.$where;
        }

        if (!$db->Execute($Query)) {
            $this->errorMsg = $db->errorMsg().$Query;
            $db = null;

            return -1;
        } else {
            $this->errorMsg = $db->errorMsg().$Query;
        }

        $this->Recordset = $db->cloneRecordSet();
        $this->FieldNames = $db->cloneFieldNames();
        $this->FieldTypes = $db->cloneFieldTypes();
        $this->RecordCount = $db->getNumOfRows();
        $this->FieldCount = $db->getNumOfFields();

        $db = null;
        $this->_setCurrentRecord();

        return $this->RecordCount;
    }

    public function findByIdRiga($id, $db)
    {
        $Query = "SELECT ";
        $Query .= "docrig.id as id_riga, docrig.id_testa, docrig.numerodoc, docrig.datadoc, docrig.codicearti, docrig.quantita, docrig.quantitare, ";
        $Query .= "docrig.unmisura, docrig.lotto, docrig.gruppo, docrig.numeroriga, docrig.rifcer as reparto, docrig.u_costk as collo, docrig.u_costk1 as bancale, ";
        $Query .= "docrig.riffromr as idRigaOrig, docrig.riffromt as idTesOrig ";
        $Query .= "FROM ";
        $Query .= "docrig  ";
        $Query .= "WHERE ";
        $Query .= "docrig.id = $id";

        if (!$db->Execute($Query)) {
            $this->errorMsg = $db->errorMsg().$Query;
            $db = null;

            return -1;
        }

        $this->Recordset = $db->cloneRecordSet();
        $this->FieldNames = $db->cloneFieldNames();
        $this->FieldTypes = $db->cloneFieldTypes();
        $this->RecordCount = $db->getNumOfRows();
        $this->FieldCount = $db->getNumOfFields();

        $db = null;
        $this->_setCurrentRecord();

        return $this->RecordCount;
    }

    public function orderBy($field)
    {

        /*
        EXAMPLE USAGE
        orderBy('age');
        orderBy(array('lastname', 'firstname'));
        */

        $data = $this->Recordset;
        if (!empty($data)) {
            if (!is_array($field)) {
                $field = array($field);
            }
            $field = array_map('strtoupper', $field);

            usort($data, function ($a, $b) use ($field) {
              $retval = 0;
              foreach ($field as $fieldname) {
                if(!$this->CaseFolding) $fieldname = strtolower($fieldname);
                if ($retval == 0) {
                  if($this->getFieldTypes($fieldname)=='integer') {
                    $retval = $a[$fieldname] - $b[$fieldname];
                  } else {
                    $retval = strnatcmp($a[$fieldname], $b[$fieldname]);
                  }
                }
              }
              return $retval;
            });
        }
        $this->Recordset = $data;
    }

    //FUNZIONI PER RICAVARE I DATI

    public function getErrorMsg()
    {
        return print '<p> Errore: '.$this->errorMsg.'<p>';
    }

    public function getRecordSet()
    {
        return $this->Recordset;
    }

    public function getCurrentRow()
    {
        return $this->_currentRow;
    }

    public function getNumOfRows()
    {
        return $this->RecordCount;
    }

    public function getNumOfFields()
    {
        return $this->FieldCount;
    }

    public function getFieldTypes($field)
    {
        $field = strtolower($field);

        return $this->FieldTypes[$field];
    }

    public function getFieldNames()
    {
        return $this->FieldNames;
    }

    public function getField($field)
    {
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

    //FUNZIONI DI POSIZIONE CURSORE

    public function moveNext()
    {
        ++$this->_currentRow;
        $this->_setCurrentRecord();
    }

    public function movePrev()
    {
        --$this->_currentRow;
        $this->_setCurrentRecord();
    }

    public function moveFirst()
    {
        $this->_currentRow = 0;
        $this->_setCurrentRecord();
    }

    public function moveLast()
    {
        $this->_currentRow = $this->Recordcount - 1;
        $this->_setCurrentRecord();
    }

    private function _setCurrentRecord()
    {
        if (0 == $this->RecordCount) {
            $this->EOF = true;
            $this->BOF = true;
        } elseif ($this->_currentRow > ($this->RecordCount - 1)) {
            $this->EOF = true;
            $this->BOF = false;
            $this->_currentRow = -1;
        } elseif ($this->_currentRow < 0) {
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
}
