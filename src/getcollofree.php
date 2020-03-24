<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 

//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$dbt1 = getODBCSocket();
$dbt2 = getODBCSocket();
$dbt3 = getODBCSocket();
$dbt4 = getODBCSocket();

$id_pl = (integer)$_GET['id'];
$collo = (integer)$_GET['collo'];
$close = isset($_GET['close']);
$extracollo = isset($_GET['extracollo']);

/* cerchiamo se esiste un collo per la pl */
$Query = "select id_term from u_termpl where id_pl = $id_pl and collo = ".$collo."";
if (!$dbt1->Execute($Query)) print "<p> 02 - C'é un errore: " . $dbt1->errorMsg() . "<p>";
if($dbt1->EOF)
{
    // il numero di collo � libero: lo impegno
    $Query = "insert into u_termpl (id_term, id_pl, collo) values (".$termid.", ".$id_pl.", ".$collo.")";
    if (!$dbt2->Execute($Query)) {
        print "<p> 02 - C'é un errore: " . $dbt2->errorMsg() . "<p>";
    }
    else
    {
        print("ok -inserted-$termid-$id_pl-$collo");
        chiudiCollo($collo);
        // impegno anche il collo successivo se c'è l'extracollo
        if($extracollo==true)
        {
            $collosup = $collo+1;
            $Query = "insert into u_termpl (id_term, id_pl, collo) values (".$termid.", ".$id_pl.", ".$collo.")";
            if (!$dbt3->Execute($Query)) {
                print "<p> 02 - C'é un errore: " . $dbt3->errorMsg() . "<p>";
            }
            else
            {
                print("ok -inserted-$termid-$id_pl-$collosup");
                chiudiCollo($collosup);
            }
        }
    }
}
else {
    $tf = $dbt1->getField(id_term);
    $termFound = (integer)$tf;
    if( $termFound == $termid)
    {
        print("ok -$termid-$termFound-$id_pl-$collo");
        chiudiCollo($collo);
        if($extracollo==true)
        {
            $collosup = $collo+1;
            print("ok -$termid-$termFound-$id_pl-$collosup");
            chiudiCollo($collosup);
        }
    }
    else
    {
        $Query = "select collo from u_termpl where id_pl = ".$id_pl." and id_term = ".$termid."";
        if (!$dbt2->Execute($Query)) print "<p> 02 - C'é un errore: " . $dbt2->errorMsg() . "<p>";
        if( !$dbt2->EOF)
        {
            print($dbt2->getField(collo));
        }
        else
        {
            $Query = "select max(collo) as lastcollo from u_termpl where id_pl = ".$id_pl." group by id_pl";
            if (!$dbt3->Execute($Query)) print "<p> 02 - C'é un errore: " . $dbt3->errorMsg() . "<p>";
            $tlast = $dbt3->getField(lastcollo);
            $last = (integer)$tlast;
            $last++;
            print("$last  -$termid-$termFound-$id_pl-$collo");
        }
    }
}

function chiudiCollo($collo) {
	global $id_pl, $close, $termid, $dbt4;
	if($close) {
		$Query = "update u_termpl set id_term = (".$termid." + 1) where id_pl = ".$id_pl." and collo = ".$collo."";
		if (!$dbt4->Execute($Query)){
            print "<p> 02 - C'é un errore: " . $dbt4->errorMsg() . "<p>";
        }
        else 
        {
		  print("-closed");
        }
	}
} 
?>
