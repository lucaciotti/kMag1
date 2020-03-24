<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$ncolli = $_POST['ncolli'];

for($j=1; $j <= $ncolli; $j++) {
    if(isset($_POST["id$j"])) {
        $Query = "update docrig";
		$Query .= " set u_costk1=" . $_POST["banc$j"];
		$Query .= ", u_costk=" . $_POST["collo$j"];
        $Query .= " where id=" . $_POST["id$j"];
        if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
	}
}

header("location: askpl-banc.php");
?>