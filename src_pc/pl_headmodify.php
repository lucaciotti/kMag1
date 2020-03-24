<?php 
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
// include("odbcSocketLib.php"); 
head("Esegui Modifica Data-Vettore");

$db = getODBCSocket();

$idTes_PL = $_GET['docid'];
$vettore = $_GET['vettore'];
$year_PL = $_GET['yearpl'];
$month_PL = $_GET['monthpl'];
$day_PL = $_GET['daypl'];

$date=date_create("$year_PL-$month_PL-$day_PL");
$datadoc=date_format($date,"Y-m-d");
print("Datadoc: ".$datadoc."<br>");
print("Vattore: ".$vettore."<br>");
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
// $db->Execute($Query);

$Query = "select id_testa from docrig where riffromt = $idTes_PL group by id_testa";
//print("$Query<br>");
$result = $db->Execute($Query);
$idTes_PB = $db->getField('id_testa');
//print($idTes_PB."<br>");

$Query = "update doctes set vettore1='$vettore', datadoc={^$datadoc} where tipodoc='PL' AND id = $idTes_PL";
//print("$Query<br>");
$result = $db->Execute($Query);

if(!empty($idTes_PB)){
    $Query = "update doctes set vettore1='$vettore', datadoc={^$datadoc} where tipodoc='PB' AND id = $idTes_PB";
    //print("$Query<br>");
    $result = $db->Execute($Query);
}

$Query = "update docrig set datadoc={^$datadoc} where tipodoc='PL' AND id_testa = $idTes_PL";
//print("$Query<br>");
$result = $db->Execute($Query);

if (!empty($idTes_PB)) {
    $Query = "update docrig set datadoc={^$datadoc} where tipodoc='PB' AND id_testa = $idTes_PB";
    //print("$Query<br>");
    $result = $db->Execute($Query);
}

print("<br> <h3>Aggiornamento Completato</h3>");

?>
<input type="button" value="Chiudi Pagina" onclick="self.close()">