<?php

include("header.php");
include("odbcSocketLib.php");

$dbReparti = getODBCSocket();
$dbOC = getODBCSocket();

$cId = $_GET['id'];

$Query = "SELECT DISTINCT docrig.rifcer FROM docrig WHERE docrig.codicearti<>'' and docrig.tipodoc=='PL' AND docrig.id_testa == $cId";
if (!$dbReparti->Execute($Query)) {
    print($dbReparti->errorMsg() . "$Query<br>");
    return -1;
}
$nReparti = $dbReparti->getNumOfRows();

$gestPB = false;
$Query = "SELECT tipodoc from docrig where docrig.riffromt==$cId";
if (!$dbOC->Execute($Query)) {
    print($dbOC->errorMsg() . "$Query<br>");
    return -1;
}
if (!$dbOC->EOF){
	$gestPB = true;
}

//print("<td colspan='2'>");
print("<div><table>\n");
print("<thead>\n");
	print("<tr>");
	    while(!$dbReparti->EOF){
	    	print("<th style='width: 60px;'>".$dbReparti->getField('rifcer')."</th>");
	    	$dbReparti->moveNext();
	    }
    print("</tr>\n");
print("</thead>\n");

print("<tbody >\n");
print("<tr>\n");
//CERCO I PREPARATI PER REPARTO __ STOP RICERCA DETTAGLIATA -> Sarà Integrata in Pagina successiva!
	$dbReparti->moveFirst();
	$totNonPrep = 0;
	$totPrep = 0;
	$totBanc = 0;
	while(!$dbReparti->EOF){
		//CONTO I NON PREPARATI
		if($gestPB){
			$Query = "SELECT COUNT(DISTINCT docrig.numeroriga) as nonprep from docrig where docrig.codicearti<>'' and docrig.quantitare>0 AND docrig.rifcer=='".$dbReparti->getField('rifcer')."' AND docrig.id_testa==".$cId."";
		} else {
			$Query = "SELECT COUNT(DISTINCT docrig.numeroriga) as nonprep from docrig where docrig.codicearti<>'' and docrig.u_costk==0 AND docrig.u_costk1==0 AND docrig.rifcer=='".$dbReparti->getField('rifcer')."' AND docrig.id_testa==".$cId."";
		}
		if (!$dbOC->Execute($Query)) {
		    print($dbOC->errorMsg() . "$Query<br>");
		    return -1;
		}
		if (!$dbOC->EOF && intval($dbOC->getField('nonprep'))!=0){
			print("<td>N.: <b>".$dbOC->getField('nonprep')."</b><br/>");
			$totNonPrep = $totNonPrep + intval($dbOC->getField('nonprep'));
		} else {
			print("<td>N.: - <br/>");
		}
		//CONTO I PREPARATI MA NON BANCALATI
		if($gestPB){
			$Query = "SELECT COUNT(DISTINCT docrig.u_costk) as prep from docrig where docrig.u_costk<>0 AND docrig.u_costk1==0 AND docrig.rifcer=='".$dbReparti->getField('rifcer')."' AND docrig.riffromt==".$cId."";
		} else {
			$Query = "SELECT COUNT(DISTINCT docrig.u_costk) as prep from docrig where docrig.u_costk<>0 AND docrig.u_costk1==0 AND docrig.rifcer=='".$dbReparti->getField('rifcer')."' AND docrig.id_testa==".$cId."";
		}
		if (!$dbOC->Execute($Query)) {
		    print($dbOC->errorMsg() . "$Query<br>");
		    return -1;
		}
		if (!$dbOC->EOF && intval($dbOC->getField('prep'))!=0){
			print("P.: <b>".$dbOC->getField('prep')."</b><br/>");
			$totPrep = $totPrep + intval($dbOC->getField('prep'));
		} else {
			print("P.: - <br/>");
		}
		//CONTO I BANCALATI
		if($gestPB){
			$Query = "SELECT COUNT(DISTINCT docrig.u_costk1) as banc from docrig where docrig.u_costk1<>0 AND docrig.rifcer=='".$dbReparti->getField('rifcer')."' AND docrig.riffromt==".$cId."";
		} else {
			$Query = "SELECT COUNT(DISTINCT docrig.u_costk1) as banc from docrig where docrig.u_costk1<>0 AND docrig.rifcer=='".$dbReparti->getField('rifcer')."' AND docrig.id_testa==".$cId."";
		}
		if (!$dbOC->Execute($Query)) {
		    print($dbOC->errorMsg() . "$Query<br>");
		    return -1;
		}
		if (!$dbOC->EOF && intval($dbOC->getField('banc'))!=0){
			print("B.: <b>".$dbOC->getField('banc')."</b></td>");
			$totBanc = $totBanc + intval($dbOC->getField('banc'));
		} else {
			print("B.: - </td>");
		}
		$dbReparti->moveNext();
	}
print("</tr>\n");
print("</tbody>\n</table>\n</div>");
//print("</td>");
?>