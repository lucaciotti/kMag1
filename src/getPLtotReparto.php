<?php

include("header.php");
//include("odbcSocketLib.php");

$dbOC = getODBCSocket();

$cId = $_GET['id'];


$Query = "SELECT COUNT(DISTINCT docrig.numeroriga) as nonprep from docrig where docrig.codicearti<>'' and docrig.u_costk==0 AND docrig.u_costk1==0 AND docrig.id_testa==$cId";
if (!$dbOC->Execute($Query)) {
    print($dbOC->errorMsg() . "$Query<br>");
    return -1;
}
if (!$dbOC->EOF && intval($dbOC->getField(nonprep))!=0){
	$totNonPrep = $totNonPrep + intval($dbOC->getField(nonprep));
}
$Query = "SELECT COUNT(DISTINCT docrig.u_costk) as prep from docrig where docrig.u_costk<>0 AND docrig.u_costk1==0 AND docrig.id_testa==$cId";
if (!$dbOC->Execute($Query)) {
    print($dbOC->errorMsg() . "$Query<br>");
    return -1;
}
if (!$dbOC->EOF && intval($dbOC->getField(prep))!=0){
	$totPrep = $totPrep + intval($dbOC->getField(prep));
}
//CONTO I BANCALATI
$Query = "SELECT COUNT(DISTINCT docrig.u_costk1) as banc from docrig where docrig.u_costk1<>0 AND docrig.id_testa==$cId";
if (!$dbOC->Execute($Query)) {
    print($dbOC->errorMsg() . "$Query<br>");
    return -1;
}
if (!$dbOC->EOF && intval($dbOC->getField(banc))!=0){
	$totBanc = $totBanc + intval($dbOC->getField(banc));
}
if($totNonPrep>0){
	print("<td style='text-align: center;' id='$cId.tot'><b style='color: red;'>N.: $totNonPrep </b><br/>");
} else {
	print("<td style='text-align: center;' id='$cId.tot'><b>N.: $totNonPrep </b><br/>");
}
print("<b>P.: $totPrep <br/>");
print("B.: $totBanc </b></td>");

?>