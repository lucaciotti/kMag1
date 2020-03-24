<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
head("Ricerca articoli PL");
?>
<script type="text/javascript">
	function cleanCode(){
		code=document.getElementById('id').value;
		if (code.indexOf("*")>-1 ){
			code = code.substring(1, 14);
			document.getElementById('id').value = code;
		}
	}
</script>

<form name="input" action="pl_02_resp.php" method="get">
<label for="id">ID Articolo:</label>
<input type="text" name="id" id="id" onchange="cleanCode();">
<input type="submit" id="btnok" value="Cerca">
</form>
<?php
setFocus("id");
goMain();
footer();

//header("Location: ../kMag2/src/menus/main.php");
//header("Location: ../kMag1/src/menus/main.php");
?>
