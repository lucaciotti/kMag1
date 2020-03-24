<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
?>
<script type="text/javascript">
//<![CDATA[
var createCookie = function(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
};

var setPrinter = function() {
	createCookie("plprinter", document.getElementById("prt").value, 10);
	return true;
};
//]]>
</script>
<?php
include("_pl_pesi.php");

$id = $_GET['id'];
$id_riga = getNumeric('id_riga');
$collo = getNumeric('collo');
$prtBanc = getNumeric('prtbanc');
$banc = getNumeric('banc');
$artCollo = ( isset($_GET['artcollo']) ? $_GET['artcollo'] : "" );
$desCollo = ( isset($_GET['descollo']) ? $_GET['descollo'] : "" );
$nColli = ( isset($_GET['ncolli']) ? $_GET['ncolli'] : 1 );
$default_prt = ( isset($_COOKIE['plprinter']) ? $_COOKIE['plprinter'] : "##" );

head("Scelta stampante");

$anno = current_year();
$peso = 0;
$error = false;

$peso = pesoCollo($id, $id_riga, $collo);
if($peso >= 0) {
	print("<form method=\"get\" action=\"pl_07_print.php\" onsubmit=\"return setPrinter();\">\n");
	hiddenField("id", $id);
	hiddenField("collo", $collo);
	hiddenField("artcollo", trim($artCollo));
	hiddenField("descollo", trim($desCollo));
	hiddenField("split", $split);
	hiddenField("ncolli", $ncolli);
	hiddenField("prtbanc", $prtBanc);
	hiddenField("banc", $banc);
	print("<select name=\"prt\" id=\"prt\">\n");
	for($i = 0; $i < count($prtlist); $i++) {
		print("<option value=\"" . $prtlist[$i] . "\"");
		print( $prtlist[$i] == $default_prt ? " selected=\"selected\">" : ">" );
		print($prtlist[$i]);
		print("</option>\n");
	}
	print("</select>\n<br>\n");

	if($peso >= 15){
		print("<span>ATTENZIONE Stampare Etichette Peso Maggiore di 15KG? (Se SI flaggare sotto)</span></br>");
		print("<input type=\"checkbox\" name=\"warnpeso\" id=\"warnpeso\" value=\"1\"> SI </br>\n");
	}
	if ($id_riga != 0){
		print("<span>Peso collo di $peso KG</span></br>");
	} else {
		print("<span>Peso dell'intera PL fino ad ora di $peso KG</span></br>");
	}

	print("<input type=\"submit\" value=\"Stampa\">\n");
	print("</form>");
} else {
	print("ERRORE CONTATTARE AMMINISTRATORE!");
}

footer();
?>
