<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Riassegna bancali ai reparti");
disableCR();
?>
<style>
label	{float: left; width: 100px;}
body	{width: 350px;}
table, td, tr 	{border-style: none; border-collapse: collapse;}
</style>
<script type="text/javascript">
//<![CDATA[
var checkCodice = function(cCodice) {
	var oRet = new Object();
	var url = "getrigadocx.php?cod=" + encodeURIComponent(cCodice);
	var milliseconds = (new Date()).getTime();
	url += "&ms=" + milliseconds;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	oRet.codice = xRet.getElementsByTagName("codicearti")[0].firstChild.nodeValue;
	if ("*error*" == oRet.codice)  {
		alert("Codice non riconosciuto");
		oRet.codice = "";
		oRet.numero = "";
		oRet.esercizio = "";
		oRet.id_testa = 0;
	} else {
		oRet.numero = xRet.getElementsByTagName("numerodoc")[0].firstChild.nodeValue;
		oRet.esercizio = xRet.getElementsByTagName("esercizio")[0].firstChild.nodeValue;
		oRet.id_testa = xRet.getElementsByTagName("id_testa")[0].firstChild.nodeValue;
	}
	return oRet;
};

var decode = function(obj) {
  var x = checkCodice(obj.value);
  document.getElementById('num').value = x.numero;
  document.getElementById('anno').value = x.esercizio;
  document.getElementById('id_testa').value = x.id_testa;
};

var conferma = function() {
	return confirm("Questa operazione riassegna, azzerando, tutta la PL\nvuoi continuare?");
}
//]]>
</script>
<label for="id">ID Articolo:</label> 
<input type="text" name="id" id="id" onblur="decode(this);">
<hr>
<form name="input" action="pl-banc.php" method="get" onsubmit="return conferma();">
<label for="num">PL numero:</label> 
<input type="text" name="num" id="num" size="4"><br>
<label for="anno">Anno:</label> 
<input type="text" name="anno" id="anno" size="4">
<hr>
<input type="submit" name="rep" id="rep" value="Riassegna bancali ai reparti">
<input type="hidden" id="id_testa" name="id_testa" value="0">
</form>
<script type="text/javascript">
//<![CDATA[
document.getElementById('anno').value = (new Date()).getFullYear();
//]]>
</script>
<?php
setFocus("id");
goMain();
footer();
?>