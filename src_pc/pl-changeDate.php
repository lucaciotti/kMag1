<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("../models/PackingList.php");
// include("odbcSocketLib.php"); 

$script="";
head_jquery_pc("Modifiche Data - Vettore PL/PB",$script);

//checkPermission();
$db = getODBCSocket();
$plInfo = new PackingList;
/*
$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);
*/
$id_testa = (isset($_GET['id_testa']) ? $_GET['id_testa'] : 0 );

$result = $plInfo->findByIdTesta($id_testa, $db);
if($result<0){
	print("<h3>ERRORE NELL'ELABORAZIONE.</h3>");
	print("<p>Errore n°:".$result."</p>");
	print("<p>".$plInfo->getErrorMsg()."</p>");
}
$plInfo->moveFirst();
$id_testa = $plInfo->getField('id_testa');

if(0 == $id_testa) {

	print("<h3>Documento non trovato.</h3>");
	print("<p>".$plInfo->getErrorMsg()."</p>");
} else {
	print("<h3 class='title'>Strumento modifica Data - Vettore PL - PB.</h3>");
	print("<div style='text-align:center;'>PL / PB n: <b>".$plInfo->getField('numerodoc')."</b><br> del <b>".$plInfo->getField('datadoc')."</b></div></br>");

	$vettore = $plInfo->getField('vettore1');
	// print($plInfo->getFieldTypes('datadoc'));
	$year_PL = date("Y", strtotime($plInfo->getField('datadoc')));
	$month_PL = date("m", strtotime($plInfo->getField('datadoc')));
	$day_PL = date("d", strtotime($plInfo->getField('datadoc')));
	
	print("<form name='headmod' action='pl_headmodify.php'>\n");
	print("<table id='headtable'>\n");
	print("<caption><b>DATI TESTA</b></caption>");
	print("<thead>\n");
		print("<tr>");
			print("<th>PL/PB</th>");
			print("<th>Cliente</th>");
			print("<th>Data</th>");
			print("<th>Vettore</th>");
			print("<th>&nbsp;</th>");
		print("</tr>\n");
	print("</thead>\n");
	
	print("<tbody>\n<tr>\n");
		print("<td style='text-align: center;'>".trim($plInfo->getField('numerodoc'))."</td>\n");
		print("<td>".trim($plInfo->getField('ragSoc')) ."</td>\n");
		print("<td style='text-align: center;'><input style=\"width: 40px\" type=\"text\" size=\"4\" id='daypl' name='daypl' value='".$day_PL."'> - <input style=\"width: 40px\" type=\"text\" size=\"4\" id='monthpl' name='monthpl' value='".$month_PL."'> - $year_PL</td>\n");
		print("<td style='text-align: center;'><select id='vettore' name='vettore'>\n");
		$Query = "select codice, descrizion from vettori order by descrizion";
		$db->Execute($Query);
		while(!$db->EOF) {
			print("<option value='".$db->getField('codice')."'");
			print($db->getField('codice') == $vettore ? " selected='selected'" : "");
			print(">".trim($db->getField('descrizion'))." [".$db->getField('codice')."]</option>\n");
			$db->MoveNext();
		}
		print("</select></td>\n");
		print("<td style='text-align: center;'><input type='image' src='../img/modify.png' height='32' alt='submit'></td>\n");
	print("</tr>\n</tbody>\n");
	print("</table>\n");
	hiddenField("docid", $id_testa);
	hiddenField("yearpl", $year_PL);
	print("</form>");
}

footer();

?>