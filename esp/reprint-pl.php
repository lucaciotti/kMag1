<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
include("../askpl-common.php");
headx("Ristampa colli");
disableCR();

print($askPLstyle);
print($askPLdecode);
print($askPLgetId);

print("<form name=\"input\" action=\"reprint-pl-select.php\" method=\"get\">\n");
print($askPLgetPl);
?>
<input type="submit" id="btnok" value="Cerca">
</form>
<?php
print($askPLsetYear);

setFocus("id");
goMain();
footer();
?>