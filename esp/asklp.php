<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
head("Ricerca articoli LP");
?>
<form name="input" action="response-lp.php" method="get">
<label for="id">ID Articolo:</label> 
<input type="text" name="id" id="id">
<input type="submit" id="btnok" value="Cerca">
</form>
<?php
setFocus("id");
goMain();
footer();
?>