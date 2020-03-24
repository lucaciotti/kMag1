<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Collocazione articoli");
?>
<style>
label	{float: left; width: 120px;}
body	{width: 350px;}
</style>
<script type="text/javascript">
//<![CDATA[
var checkCodiceArtix = function(cCodice, cCF)  {
	var url = "getcodiceartix.php?cod=" + encodeURIComponent(cCodice);
	if (cCF != "") {
		url = url + "&cf=" + cCF;
	}
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
		oRet.descrizion = xRet.getElementsByTagName("descrizion")[0].firstChild.nodeValue;
	    var tmp = xRet.getElementsByTagName("ubicazione")[0];
		if (tmp.childNodes.length) {
			oRet.ubicazione = tmp.firstChild.nodeValue;
		} else {
			oRet.ubicazione = "";
		}
	}
	return oRet;
};

var decode = function(obj) {	
	if(obj.value=="") return;
	var oArt = checkCodiceArtix(obj.value);
	obj.value = oArt.codice;
	document.getElementById("ubicazione").value = oArt.ubicazione;
	document.getElementById("descrizion").value = oArt.descrizion;
};
//]]>
</script>
<label for="reqcodice">Articolo:</label> 
<input type="text" id="reqcodice" onblur="decode(this);"/><br>
<input type="text" id="descrizion" size="40" readonly="readonly" /><br>
<label for="ubicazione">Ubicazione:</label>
<input type="text" id="ubicazione" size="5" value=""/><br>
<input type="submit" value="Ok" onclick="sendUbicazione();"/><br>
<?php
setFocus("reqcodice");
goMain();
footer();
?>