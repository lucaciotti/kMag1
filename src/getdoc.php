<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Ricerca documento");
?>
<script>
function checkIdDoc(cCodice)  {
	if (cCodice.substring(0,3) == "293") {
		var url = "getIdDoc.php?cod=" + encodeURIComponent(cCodice.substring(3,12));
		makeHttpXml();
		httpXml.open("GET", url, false);
		httpXml.send(null);
		var cRet = httpXml.responseText;
		if ("*error*" == cRet)  {
			alert("Documento non trovato");
			cRet = "";
		}
		return cRet;
	} else {
	    alert("Codice non valido"); 
		return "";
	}
}

function decode(obj) {
  obj.value = checkIdDoc(obj.value);
  var tbox = document.getElementById('id');
  tbox.value = obj.value;
}
</script>
Barcode documento: 
<input type="text" onblur="decode(this);"/>

<form name="input" action="testadoc-lista.php" method="get" >
<input type="hidden" name="id" id="id" />
<input type="hidden" name="user" id="user" />
<input type="hidden" name="mode" id="mode" value="b" />
<input type="submit" id="btnok" value="Cerca" />
</form>
<?php
print ("<br/><a href=\"index.php\"><img noborder src=\"b_home.gif\"/>Menu principale</a>\n");
footer();
?>