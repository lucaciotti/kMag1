<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
headx("Ricerca articoli");
?>
<script type="text/javascript">
function decode(obj) {
	if(obj.value=="") return;
  obj.value = checkCodiceArti(obj.value);
  var tbox = document.getElementById('articolo');
  tbox.value = obj.value;
}
</script>
<label for="id">Articolo:</label> 
<input type="text" id="id" onblur="decode(this);"/>
<?php
setFocus("id");
?>
<form name="input" action="response-ole.php" method="get" >
<input type="hidden" name="articolo" id="articolo" />
<input type="submit" id="btnok" value="Cerca" onclick="decode(id);" />
</form>
<?php
goMain();
footer();
?>