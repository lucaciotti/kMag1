<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 

//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);

$dbtest = getODBCSocket();
$dbtest2 = getODBCSocket();
$dbtest3 = getODBCSocket();
$dbWrite = getODBCSocket();

$id_pl = $_GET['id'];
$banc = $_GET['banc'];
$rep = trim($_GET['rep']);
$close = isset($_GET['close']);

//head('Test');

// cerchiamo se esiste un collo per la pl 
$Query = "select reparto from u_bancpl where id_pl = $id_pl and bancale = $banc";
if (!$dbtest->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbtest->errorMsg() . "<p>";

if($dbtest->EOF) {
    // il numero di collo è libero: lo impegno
    $Query = "insert into u_bancpl (reparto, id_pl, bancale) values ('".$rep."', $id_pl, $banc)";
    if (!$dbWrite->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "<p>";
    print("ok-inserted-$rep-$id_pl-$banc");
    chiudiCollo($banc);
} else {
    $tf = trim($dbtest->getField(reparto));
    if( $tf == $rep)
    {
		// continuiamo un collo già prenotato
        print("ok-$rep-$tf-$id_pl-$banc");
        chiudiCollo($banc);
    } else {
        $Query = "select bancale from u_bancpl where id_pl = $id_pl and reparto = '".$rep."'";
        if (!$dbtest2->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbtest2->errorMsg() . "<p>";
        if( !$dbtest2->EOF) {
			// popongo un collo aperto utilizzabile
            print($dbtest2->getField(bancale));
        } else {
			// nuovo numero collo
            $Query = "select max(bancale) as lastcollo from u_bancpl where id_pl = $id_pl group by id_pl";
            if (!$dbtest3->Execute($Query)) print "<p> 01 - C'é un errore: " . $dbtest3->errorMsg() . "<p>";
            $tlast = $dbtest3->getField(lastcollo);
            $last = (integer)$tlast;
            $last++;
            print("$last  -$rep-$tf-$id_pl-$banc");
        }
    }
}

function chiudiCollo($collo) {
	global $id_pl, $close, $rep, $dbWrite;
	if($close == true) {
		$Query = "update u_bancpl set reparto = '@'+reparto where id_pl = $id_pl and bancale = $collo";
		if (!$dbWrite->Execute($Query)) {
            print "<p> 01 - C'é un errore: " . $dbWrite->errorMsg() . "</p>";
        } else {
            print("-closed");
        }
	}
} 

?>
