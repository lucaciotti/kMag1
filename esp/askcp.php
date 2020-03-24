<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Carico produzione");
?>
<style>
label	{float: left; width: 150px;}
body	{width: 350px;}
</style>
<script type="text/javascript">
//<![CDATA[
var decode = function(obj) {
    var cCodice = obj.value;
	var tbox = document.getElementById('articolo');
	var qtaObj = document.getElementById('qta');
	var writableObj = document.getElementById(obj.id);
	if (cCodice.substring(0,3) != "292" && cCodice.substring(0,3) != "293") {
		// carico estemporaneo
		tbox.value = checkCodiceArti(cCodice);
		writableObj.value = tbox.value;
		// prendo la quantità dal lotto
	} else {
		// riga documento prendo codice e quantità dalla riga
		var url = "getrigadoc.php?cod=" + encodeURIComponent(cCodice);
		makeHttpXml();
		httpXml.open("GET", url, false);
		httpXml.send(null);
		var cRet = httpXml.responseText;
		var codiceArti = cRet.substr(0,20);
		var quantita = cRet.substr(21);
		if ("*error*" == cRet)  {
			alert("Codice non riconosciuto");
			cRet = "";
		}
		tbox.value = codiceArti;
		writableObj.value = codiceArti;
		qtaObj.value = quantita;
	}
};
//]]>
</script>
<label for="master">Articolo / Ordine:</label> 
<input type="text" id="master" onblur="decode(this);"/>
<?php
setFocus("master");
?>

<form name="input" action="esplodi.php" method="get" >
<input type="hidden" name="articolo" id="articolo" />
<input type="hidden" name="mode" id="mode" value="CP" />
<label for="qta">Quantita:</label>
<input type="input" name="qta" id="qta" /><br>
<input type="submit" id="btnok" value="Ok" />
</form>
<?php
goMain();
footer();
?>