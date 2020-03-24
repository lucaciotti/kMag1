<?php

/************************************************************************/

/* Project ArcaWeb                               		        		*/

/* ===========================                                          */

/*                                                                      */

/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */

/*                                                                      */

/************************************************************************/

include ("header.php");
head_jquery_pc("Modifica PL / OC", '');
disableCR();

checkPermission();

print("USER:". $_SESSION['UserPL'] );
?>

<script type="text/javascript" src="../jquery-1.10.2.min.js"></script>
<script type="text/javascript">
	//<![CDATA[
	var checkCodice = function(cCodice) {
		if("" === cCodice) {
			return null;
		}
		var oRet = {
			codice: "",
			numero: "",
			esercizio: "",
			id_testa: 0
		};
		var xRet = $.ajax({
			type: "GET",
			url: "getrigadocx.php?cod=" + encodeURIComponent(cCodice),
			async: false,
			error: function() {
				alert("Errore leggendo la riga documento.");
			}
		}).responseXML;
		oRet.codice = $(xRet).find("codicearti").text();
		if ("*error*" == oRet.codice)  {
			alert("Codice non riconosciuto");
			oRet.codice = "";
		} else {
			oRet.numero = $(xRet).find("numerodoc").text();
			oRet.esercizio = $(xRet).find("esercizio").text();
			oRet.id_testa = $(xRet).find("id_testa").text();
		}
		return oRet;
	};

	var decode = function(obj) {
		var x = checkCodice(obj.value);
		if( x != null) {
			$('#num').val(x.numero);
			$('#anno').val(x.esercizio);
			$('#id_testa').val(x.id_testa);
		}
	};
	//]]>
</script>

<h3 class='title'>Seleziona Packing List.</h3>
<div style='text-align:center;'>
	<form name="input" action="pl-edit.php" method="get">
		<label for="num">PL / PB numero:</label>
		<input type="text" name="num" id="num" size="4"><br>
		<label for="anno">Anno:</label>
		<input type="text" name="anno" id="anno" size="4">
		<hr>
		<input type="submit" name="btnok" id="btnok" value="CERCA">
				
		</tr></table>
		<input type="hidden" id="id_testa" name="id_testa" value="0">
	</form>
</div>
<script type="text/javascript">
	//<![CDATA[
	$('#anno').val((new Date()).getFullYear());
	//]]>
</script>
<?php

/*
<style>
	label	{float: left; width: 100px;}
	body	{width: 350px;}
	table, td, tr 	{border-style: none; border-collapse: collapse;}
</style>

<label for="id">ID Articolo:</label>
<input type="text" name="id" id="id" onblur="decode(this);">
<hr>
*/
setFocus("id");
logOut();
goMain();
footer();
?>