<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Dimensioni bancali");
?>

<script type="text/javascript">
//<![CDATA[
var checkCodiceArtix = function(cCodice)  {
	var url = "getcodiceartix.php?cod=" + encodeURIComponent(cCodice);
	var milliseconds = (new Date()).getTime(); 
	url += "&x=" + milliseconds;

	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;

	var oRet = new Object();
	oRet.codice = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
	if ("*error*" == oRet.codice)  {
		alert("Codice non riconosciuto");
		oRet.codice = "";
	} else {
		oRet.xml = xRet;
	}
	return oRet;
};

var decodeBanc = function(obj,n) {
	if("" == obj.value) {
		return;
	}
	var oArt = checkCodiceArtix(obj.value);
	document.getElementById("misural"+n).value = oArt.xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue;
	document.getElementById("misuras"+n).value = oArt.xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;
};
//]]>
</script>

<?php
$db = getODBCSocket();
$db2 = getODBCSocket();
$db3 = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
//
//$num = str_pad(trim($_POST['num']), 6, " ", STR_PAD_LEFT) . "  ";
//$anno = trim($_POST['anno']);
$ncolli = $_POST['ncolli'];
$id_testa = $_POST['id_testa'];

// aggiorniamo le righe della packing list
if(isset($_POST['rep1'])) {
	// gestione a reparti
	for($j=1; $j <= $ncolli; $j++) {
		$currep = $_POST["rep$j"];
		$Query = "update docrig set u_costk1=" . $_POST["collo$j"];
		$Query .= " where id_testa=$id_testa and rifcer='".$currep."'";
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" ;
	}
} else {
	// gestione a colli
//    echo "Gestione a colli";
	for($j=1; $j <= $ncolli; $j++) {
//        print("<br>$j");
        if(isset($_POST["collo$j"])){
            $Query = "update docrig set u_costk1=" . $_POST["collo$j"];
            $Query .= " where id_testa=$id_testa and u_costk=$j";
           if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" ;
            // sistemo anche le dimensioni dei colli
            $Query = "update docrig set ";
            $Query .= "u_misural = " . $_POST["imb_u_misural$j"];
            $Query .= ", u_misurah = " . $_POST["imb_u_misurah$j"];
            $Query .= ", u_misuras = " . $_POST["imb_u_misuras$j"];
            $Query .= ", prezzoacq = " . $_POST["peso$collo"];
            $Query .= ", codicearti = '" . $_POST["art$j"] . "'";
            $Query .= " where id = " . $_POST["id_riga$j"];
//            print("$Query");
           if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" ;
        }
	}
}

// adesso chiedo le misure
print("<form method=\"post\" action=\"pl-banc-size.php\">\n");
print("<table>\n<tr><th>Bancale</th><th>Articolo / Dimensioni</th><th>Peso</th></tr>\n");

$Query = "select u_costk1, max(u_traverso) as altezza from docrig ";
$Query .= "where id_testa=$id_testa and u_costk1 != 0 ";
$Query .= "order by u_costk1 ";
$Query .= "group by u_costk1 ";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" ;
$ncolli = 0;
while(!$db->EOF) {
	$ncolli++;
	$size = $db->getField(altezza);
	$banc = $db->getField(u_costk1);
	print("<tr>\n<td rowspan=\"2\">$banc</td>\n");
	print("<td>\n");

	$pal = "";
	$traverso = 0;
	$misural = 0;
	$misuras = 0;
	$idRigaPallet =0;
	print("<select id=\"codbanc$banc\" name=\"codbanc$banc\" onchange=\"decodeBanc(this, $banc);\">\n");
	print("<option value=\"\"> - Scegli bancale - </option>\n");
	// cerco se esiste gia l'articolo pallet
	$Query = "select codicearti, id, u_traverso, u_misural, u_misuras, prezzoacq from docrig where id_testa=$id_testa and u_costk1=$banc and u_costk=0";
	if (!$db2->Execute($Query)) print "<p> 01 - C'é un errore: " . $db2->errorMsg() . "<p>" and die();
	if(!$db2->EOF) {
		$pal = $db2->getField(codicearti);
		$misural = $db2->getField(u_misural);
		$misuras = $db2->getField(u_misuras);
		$traverso = $db2->getField(u_traverso);
		$idRigaPallet = $db2->getField(id);
		$pesoBanc = $db2->getField(prezzoacq);
	}
	$Query = "select magart.codice, magart.descrizion from magart where magart.codice in (select u_pallet.codice from u_pallet)";
	if (!$db3->Execute($Query)) print "<p> 01 - C'é un errore: " . $db3->errorMsg() . "<p>" and die();
	while(!$db3->EOF) {
		print("<option value=\"" . trim($db3->getField(codice)) . "\"");
		if($db3->getField(codice) == $pal) {
			print(" selected=\"selected\"");
		}
		print(">" . trim($db3->getField(descrizion)) . "</option>\n"); 
		$db3->MoveNext();
	}
	print("</select>\n");
	print("<td rowspan=\"2\">");
	print("<input type=\"text\" size=\"4\" name=\"peso$banc\" id=\"peso$banc\" value=\"$pesoBanc\">\n");
	print("</td>\n</tr>\n");
	print("<tr>\n<td>\n");
	print("<input type=\"text\" size=\"4\" name=\"misural$banc\" id=\"misural$banc\" value=\"$misural\">\n");
	print("<input type=\"text\" size=\"4\" name=\"misuras$banc\" id=\"misuras$banc\" value=\"$misuras\">\n");
	print("<input type=\"text\" size=\"4\" name=\"misurah$banc\" id=\"misurah$banc\" value=\"$traverso\">\n");
	print("&nbsp;(L x P x H)");
	hiddenField("idpal$banc",$idRigaPallet);
	print("</td>\n</tr>\n");
	$db->MoveNext();
}

print("</table>\n<br>\n");
print("<input type=\"submit\" value=\"ok\">\n");
//hiddenField("num",trim($num));
//hiddenField("anno",$anno);
hiddenField("nbanc",$ncolli);
hiddenField("id_testa",$id_testa);
print("</form>\n");

print ("<br><br>\n<a class=\"menu\" href=\"askpl-banc.php\">Altra ricerca</a>\n<br>\n");
goMain();
footer();
?>