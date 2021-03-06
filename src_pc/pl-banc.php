<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
$script = <<< EOT
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
	$("#maintable1").tablesorter( {sortList: [[0,0]]} );
//	$("#maintable2").tablesorter( );
	$("#maintable3").tablesorter( );
} );

var delCollo = function(nCollo) {
	var id_testa = $("#id_testa").val();
	$.get("pl-delcolli.php", { id: id_testa, mode: "X", collo: nCollo } );
	alert("Collo "+nCollo+" cancellato.");
	location.reload(true);
};
//]]>
</script>
EOT;

head_jquery("Gestione bancali",$script);
disableCR();
?>
<script type="text/javascript">
//<![CDATA[
var setMisure = function(nCollo)  {
	var cCodice = $("#art"+nCollo).val();
	$.ajax({
		type: "GET",
		url: "getcodiceartix.php",
		data: { cod: cCodice },
		dataType: "xml",
		success: function(xml) {
			var v = xml.getElementsByTagName("u_misural")[0].firstChild.nodeValue;
			if(v!= 0) {
				$("#imb_u_misural"+nCollo).val(v)
			}
			v = xml.getElementsByTagName("u_misurah")[0].firstChild.nodeValue;
			if(v != 0) {
				$("#imb_u_misurah"+nCollo).val(v)
			}
			v = xml.getElementsByTagName("u_misuras")[0].firstChild.nodeValue;
			if(v != 0) {
				$("#imb_u_misuras"+nCollo).val(v)
			}
		}
    });
};
//]]>
</script>
<?php
$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);
$num = str_pad(trim($_GET['num']), 6, " ", STR_PAD_LEFT) . "  ";
$anno = trim($_GET['anno']);
$id_testa = $_GET['id_testa'];
$rep = isset($_GET['rep']);
$art = isset($_GET['art']);
$gestbanc = isset($_GET['gestbanc']);

if(0 == $id_testa) {
	$Query = "select id from doctes where tipodoc=\"PB\" and esercizio=\"$anno\" and numerodoc=\"$num\"";
	$rs = $conn->Execute($Query);
	if(!$rs->EOF) {
		$id_testa = $rs->Fields['id']->Value;
	}
}

if(0 == $id_testa) {
	print("<h3>Documento non trovato.</h3>");
} else {
	if($art) {
		// gestione ad articoli
		print("<form method=\"post\" id=\"write\" action=\"pl-art-write.php\">\n");
		print("<table id=\"maintable3\">\n<thead>\n<tr>");
		print("<th>Riga</th><th>Codice</th><th>Descrizione</th><th>Q.ta</th><th>Reparto</th><th>Collo</th><th>Bancale</th>");
		print("</tr>\n</thead>\n");
		print("<tbody>\n");
		$Query = "select numeroriga, id, codicearti, descrizion, quantita, rifcer, u_costk, u_costk1 ";
		$Query .= "from docrig where id_testa = $id_testa order by u_costk, u_costk1";
		$rs = $conn->Execute($Query);
		$ncolli = 0;
		while(!$rs->EOF) {
			$ncolli++;
			$reparto = trim($rs->Fields['rifcer']->Value);
			$articolo = trim($rs->Fields['codicearti']->Value);
			$descrizion = trim($rs->Fields['descrizion']->Value);
			$quantita = trim($rs->Fields['quantita']->Value);
			print("<tr>\n");
			print("<td>" . $rs->Fields['numeroriga']->Value . "</td>\n");
			print("<td>");
			hiddenField("id$ncolli", $rs->Fields['id']->Value);
			print("$articolo</td>\n<td>$descrizion</td>\n<td>$quantita</td>\n<td>$reparto</td>\n");
			print("<td><input id=\"collo$ncolli\" name=\"collo$ncolli\" type=\"text\" size=\"3\" value=\"" . $rs->Fields['u_costk']->Value . "\"></td>\n");
			print("<td><input id=\"banc$ncolli\" name=\"banc$ncolli\" type=\"text\" size=\"3\" value=\"" . $rs->Fields['u_costk1']->Value . "\"></td>\n");
			print("</tr>\n");
			$rs->MoveNext();
		}
		print("</tbody>\n</table>\n");
		print("<input type=\"submit\" value=\"ok\">\n");
		hiddenField("ncolli",$ncolli);
		print("</form>\n");
	} else {
		print("<form method=\"post\" id=\"write\" action=\"pl-banc-write.php\">\n");
		if($rep) {
			// gestione a reparto
			print("<table id=\"maintable1\">\n<thead>\n<tr><th>Reparto</th><th>Bancale</th></tr>\n</thead>\n");
			print("<tbody>\n");
			$Query = "select distinct rifcer from docrig ";
			$Query .= "where id_testa=$id_testa and u_costk != 0 ";
			$Query .= "order by rifcer ";
			$rs = $conn->Execute($Query);
			$ncolli = 0;
			while(!$rs->EOF) {
				$ncolli++;
				$reparto = trim($rs->Fields['rifcer']->Value);
				print("<tr>\n<td><input type=\"text\" readonly=\"readonly\" name=\"rep$ncolli\" id=\"rep$ncolli\" value=\"$reparto\"></td>\n");
				print("<td><input type=\"text\" size=\"4\" name=\"collo$ncolli\" id=\"collo$ncolli\" value=\"0\"></td>\n</tr>\n");
				$rs->MoveNext();
			}
			print("</tbody>\n</table>\n");
		} else {
			// gestione a colli
			print("<table id=\"maintable2\">\n<thead>\n<tr><th>Collo</th><th>Bancale</th><th>Reparto</th>");
			print("<th>Dimensioni&nbsp;(L x P x H) in mm</th><th>Peso</th><th>Del</th></tr>\n</thead>\n");
			print("<tbody>\n");
			$Query = "select docrig.u_costk, docrig.u_costk1, docrig.rifcer, docrig.codicearti, ";
			$Query .= "docrig.u_misural, docrig.u_misuras, docrig.u_misurah, docrig.id, docrig.prezzoacq ";
			$Query .= "from docrig inner join magart on magart.codice = docrig.codicearti ";
			$Query .= "where docrig.id_testa=$id_testa and docrig.u_costk != 0 and magart.danger ";
			$Query .= "order by docrig.u_costk ";
			$rs = $conn->Execute($Query);
			$ncolli = 0;
			while(!$rs->EOF) {
				$ncolli++;
				$collo = $rs->Fields['u_costk']->Value;
				$banc = $rs->Fields['u_costk1']->Value;
				$reparto = trim($rs->Fields['rifcer']->Value);
				$art = trim($rs->Fields['codicearti']->Value);
				$peso = $rs->Fields['prezzoacq']->Value;
				print("<tr>\n");
					print("<td rowspan=\"2\">$collo\n");
					hiddenField("row$ncolli",$collo);
					print("</td>\n");
					print("<td rowspan=\"2\"><input type=\"text\" size=\"4\" name=\"collo$collo\" id=\"collo$collo\" value=\"$banc\"></td>\n");
					print("<td rowspan=\"2\">$reparto</td>\n");
					print("<td>\n");
				$Query = "select codice, descrizion from magart where danger";
				$rsa = $conn->Execute($Query);
				print("<select id=\"art$collo\" name=\"art$collo\" onchange=\"setMisure($collo);\">\n");
				while(!$rsa->EOF) {
					print("<option value=\"" . trim($rsa->Fields['codice']->Value) . "\"");
					if(trim($rsa->Fields['codice']->Value) == $art) {
						print(" selected=\"selected\"");
					}
					print(">" . trim($rsa->Fields['descrizion']->Value) . "</option>\n");
					$rsa->MoveNext;
				}
				print("</select>\n");
				print("</td>\n");

					print("<td rowspan=\"2\"><input type=\"text\" size=\"4\" name=\"peso$collo\" id=\"peso$collo\" ");
					print("value=\"" . $peso . "\">\n");
					print("</td>\n");
				
				print("<td rowspan=\"2\"><img src=\"../b_drop.png\" onclick=\"delCollo($collo);\"></td>\n");

				print("</tr>\n");
				print("<tr>\n<td>\n");
				print("<input type=\"text\" size=\"4\" name=\"imb_u_misural$collo\" id=\"imb_u_misural$collo\" ");
				print("value=\"" . $rs->Fields['u_misural']->Value . "\">\n");
				print("<input type=\"text\" size=\"4\" name=\"imb_u_misuras$collo\" id=\"imb_u_misuras$collo\" ");
				print("value=\"" . $rs->Fields['u_misuras']->Value . "\">\n");
				print("<input type=\"text\" size=\"4\" name=\"imb_u_misurah$collo\" id=\"imb_u_misurah$collo\" ");
				print("value=\"" . $rs->Fields['u_misurah']->Value . "\">\n");
				hiddenField("id_riga$collo", $rs->Fields['id']->Value);
				print("</td>\n</tr>\n");
				$rs->MoveNext();
			}
			print("</tbody>\n</table>\n");
		}
		print("<input type=\"submit\" value=\"ok\">\n");
		hiddenField("ncolli",$ncolli);
		hiddenField("id_testa",$id_testa);
		print("</form>\n");
	}
	if($gestbanc) {
		print("<script type=\"text/javascript\">\n");
		print("//<![CDATA[\n");
		print("document.forms[\"write\"].submit();\n");
		print("//]]>\n");
		print("</script>\n");
	}
}
print ("<br><br>\n<a class=\"menu\" href=\"askpl-banc.php\">Altra ricerca</a>\n<br>\n");
goMain();
footer();
?>