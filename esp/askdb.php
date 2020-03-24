<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Lettura cartellino");
?>
<script type="text/javascript">
//<![CDATA[
var decode = function(obj) {
	if(obj.value=="") return;
  obj.value = checkCodiceArti(obj.value);
  var tbox = document.getElementById('articolo');
  tbox.value = obj.value;
};
//]]>
</script>
<label for="code">Articolo / Ordine:</label> 
<input type="text" name="code" id="code" onblur="decode(this);"/>
<?php
setFocus("code");
?>

<form name="input" action="esplodi.php" method="get" >
<input type="hidden" name="articolo" id="articolo" />
<label for="qta">Quantita:</label>
<input type="input" name="qta" id="qta" /><br>
<input type="submit" id="btnok" value="Ok" />
</form>
<?php
goMain();
footer();
?>