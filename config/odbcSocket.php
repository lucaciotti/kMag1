<?php

//Mi permette di costruire quante pi� connessioni necessito
function getODBCSocket(){
	// $db = new ODBCSocketServer;
	// $db ->HostName = "172.16.2.9";
	// $db ->Port = 9629;
	// $db ->ConnectionString="Provider=vfpoledb.1;Data Source=d:\arca\arca\Arca_Spagna\ditte\Spagna\Private.dbc;Collating Sequence=Machine";
	$db = new ODBCSocketServer;
	$db->_setHostName("172.16.2.102");
	$db->_setPort(9628);
	$db->_setConnectionString("Provider=vfpoledb.1;Data Source=d:\arca\Arca_Spagna\ditte\Spagna\Private.dbc;Collating Sequence=Machine");
	return $db;
}