<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Gestione colli PL");
?>
<script type="text/javascript" src="select_lib.js"></script>
<script type="text/javascript">
// <![CDATA[ 
var getColli = function(id)  {
	document.getElementById("collo").innerHTML = "";
	var url = "getcollix.php?id=" + id;
	var milliseconds = new Date().getTime(); 
	url += "&x=" + milliseconds;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	var id_testa = document.getElementById("id_testa");
	id_testa.value = xRet.getElementsByTagName("id_testa")[0].firstChild.nodeValue;
	var oList = xRet.getElementsByTagName("collo");
	var oDoc;
	for( var j=0; j<oList.length; j++) {
	  oDoc = oList[j];
      appendOptionLast("collo", oDoc.firstChild.nodeValue);
	}
	appendOptionLast2("collo","Tutti i colli",0);
};

var gestColli = function(mode) {
	var url = "pl-delcolli.php?id=" + document.getElementById("id_testa").value;
	url += "&collo=" + document.getElementById("collo").value;
	url += "&mode=" + mode;
	window.location.href = url;
};

var riapriColli = function() {
	gestColli("R");
}

var cancellaColli = function() {
	gestColli("C");
}
// ]]> 
</script>
<label for="id">ID Articolo:</label> 
<input type="text" name="id" id="id" onchange="getColli(this.value);">
<input type="hidden" name="id_testa" id="id_testa">
<br>
<label for="collo">Collo:</label>
<select name="collo" id="collo">
</select>
<br><br>
<input type="button" value="Riapri colli" onclick="riapriColli();">
&nbsp;&nbsp;
<input type="button" value="Cancella colli" onclick="cancellaColli();">
<br>
<?php
setFocus("id");
goMain();
footer();
?>