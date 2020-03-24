<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("_pl_pesi.php");

headx("Imballi");
disableCR();

$db = getODBCSocket();

// $conn = new COM("ADODB.Connection");
// $conn->Open($connectionstring);

$id = $_GET['id'];
$id_pl = $_GET['id_pl'];
$collo = getNumeric('collo');
$prt = getNumeric('prt');
$artCollo = getString('artcollo');
$desCollo = getString('descollo');
$nColli = getNumeric('ncolli');

?>
<style>
label	{float: left; width: 60px;}
label.checkbox	{float: none; width: 100px;}
body	{width: 250px;}
</style>
<script type="text/javascript" src="../pl-imb-util.js"></script>
<?php
// calcolo dei pesi teorici
$pesocollo = pesoCollo($id_pl, $id, $collo);

print("<div style=\"width: 250px\"><form name=\"plimb\" method=\"get\" action=\"pl_05_wrImb.php\" onsubmit=\"return checkForm();\">\n");
hiddenField("id",$id);
hiddenField("id_pl",$id_pl);
hiddenField("collo",$collo);
hiddenField("prt",$prt);
hiddenField("artcollo",$artCollo);
hiddenField("descollo",$desCollo);
hiddenField("ncolli",$nColli);

print("<label for=\"art\">Imballo</label>\n");
print("<input type=\"text\" id=\"art\" name=\"art\" onblur=\"decodeImb(this);\">\n");
//print("<input type=\"text\" id=\"art\" name=\"art\" onblur=\"alert(this.value);\">\n");
print("<br>\n");
print("<fieldset>\n<legend>Dimensioni</legend>\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"imb_u_misural\" id=\"imb_u_misural\" ");
print("onblur=\"copiaVal(this, document.getElementById('pal_u_misural'));\">&nbsp;X\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"imb_u_misuras\" id=\"imb_u_misuras\" ");
print("onblur=\"copiaVal(this, document.getElementById('pal_u_misuras'));\">&nbsp;X\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"imb_u_misurah\" id=\"imb_u_misurah\" ");
print("onblur=\"copiaVal(this, document.getElementById('altezza'));\"> in cm\n");
print("</br>(Largh. x Profondit� x (H)Altezza)");
print("</fieldset>\n");

// Roberto 16.09.2014
// Richiesta peso collo
print("<div id=\"divpesocollo\">\n");
print("<fieldset>\n<legend>Peso collo in kg</legend>\n");
print("<input type=\"text\" size=\"6\" name=\"pesocollo\" id=\"pesocollo\" onblur=\"chkPesoCollo(this, $collo);\">\n");
print("</fieldset>\n<br>\n");
print("</div>\n");

// Roberto 10.10.2014
// Gestiamo ingombri e pesi del secondo collo
print("<div id=\"collo2\" style=\"background: #c0ffc0; display:" . ($nColli > 1 ? "block" : "none") . ";\">\n");
print("Secondo collo<br>\n");
print("<label for=\"art\">Imballo</label>\n");
print("<input type=\"text\" id=\"art2\" name=\"art2\" onblur=\"decodeImb2(this);\">\n");
print("<br>\n");
print("<fieldset>\n<legend>Dimensioni</legend>\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"imb_u_misural2\" id=\"imb_u_misural2\" ");
print("onblur=\"copiaVal(this, document.getElementById('pal_u_misural'));\">&nbsp;X\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"imb_u_misuras2\" id=\"imb_u_misuras2\" ");
print("onblur=\"copiaVal(this, document.getElementById('pal_u_misuras'));\">&nbsp;X\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"imb_u_misurah2\" id=\"imb_u_misurah2\" ");
print("onblur=\"copiaVal(this, document.getElementById('altezza'));\"> in cm\n");
print("</br>(Largh. x Profondit� x (H)Altezza)");
print("</fieldset>\n");
// Richiesta peso collo
print("<div id=\"divpesocollo2\">\n");
print("<fieldset>\n<legend>Peso collo in kg</legend>\n");
print("<input type=\"text\" size=\"6\" name=\"pesocollo2\" id=\"pesocollo2\" onblur=\"chkPesoCollo(this, $collo+1);\">\n");
print("</fieldset>\n<br>\n");
print("</div>\n");
print("</div>\n");
// fine secondo collo

print("<label for=\"rep\">Reparto</label>\n");
selectReparti($db, $id);
print("<br>\n");

print("<input type=\"checkbox\" id=\"hasbanc\" name=\"hasbanc\" value=\"hasbanc\" onclick=\"clickBancale();\">\n");
print("<label for=\"hasbanc\" class=\"checkbox\">Bancalato</label>\n");
print("<div id=\"askbanc\" style=\"background: #c0e0ff; display: none;\">\n");
print("<label for=\"bancnum\">Bancale</label>\n");
print("<input type=\"text\" id=\"bancnum\" name=\"bancnum\" size=\"2\" value=\"1\">\n");
print("<input type=\"checkbox\" id=\"closebanc\" name=\"closebanc\" value=\"close\" onclick=\"showHideText(this, 'askcodbanc');\">\n");
print("<label for=\"closebanc\" class=\"checkbox\">Chiudi bancale</label>\n");
print("<div id=\"askcodbanc\" style=\"display: none;\">\n");
print("<label for=\"codbanc\">Tipo</label>\n");
print("<select style=\"width: 280px; font-size: 78%\" id=\"codbanc\" name=\"codbanc\" onchange=\"decodeBanc(this);\">\n");
print("<option value=\"NONE\"> - Scegli bancale - </option>\n");
$Query = "select magart.codice, magart.descrizion from magart where magart.codice in (select u_pallet.codice from u_pallet)";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "</p>";
while(!$db->EOF) {
	print("<option value=\"" . trim($db->getField(codice)) . "\">" . trim($db->getField(descrizion)) . "</option>\n");
	$db->MoveNext();
}
print("</select>\n");
print("<fieldset>\n<legend>Dimensioni</legend>\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"pal_u_misural\" id=\"pal_u_misural\">&nbsp;X\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"pal_u_misuras\" id=\"pal_u_misuras\">&nbsp;X\n");
print("<input style=\"width: 40px\" type=\"text\" size=\"4\" name=\"altezza\" id=\"altezza\"> in cm\n");
print("</br>(Largh. x Profondit� x (H)Altezza)");
print("</fieldset>\n");

// Roberto 16.09.2014
// Richiesta peso bancale
print("<fieldset><legend>Peso bancale in kg</legend>\n");
//print("<input type=\"text\" size=\"4\" name=\"pesobanc\" id=\"pesobanc\" onblur=\"chkPesoBanc(this, document.getElementById('bancnum').value);\">\n");
print("<input type=\"text\" size=\"4\" name=\"pesobanc\" id=\"pesobanc\">\n");
print("</fieldset>\n");

// Roberto 30.09.2014
// Richiesta stampa bancale
print("<input type=\"checkbox\" id=\"prtbanc\" name=\"prtbanc\" value=\"1\">\n");
print("<label for=\"prtbanc\" class=\"checkbox\">Stampa bancale</label>\n");

print("</div>\n");
print("</div>\n");

print("<br>\n");

print("<input style=\"float: right\" type=\"submit\" value=\"Procedi\">\n");
print("</form> </div>\n");
setFocus("art");
print("<br>\n<br>\n");
footer();

function getReparto($db, $id) {
	$Query = "select codicearti, rifcer from docrig where id = $id";
	if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die;
	$rep = trim($db->getField(rifcer));
	if("" == $rep) {
    $Query = "select u_reparto from magart where codice = '" . $db->getField(codicearti) . "'";
		if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die;
		$rep = trim($db->getField(u_reparto));
	}
	return $rep;
}

function selectReparti($db, $id) {
	print("<select id=\"rep\" name=\"rep\">\n");
	reparti(getReparto($db, $id), $db);
	print("</select>\n");
}
?>
