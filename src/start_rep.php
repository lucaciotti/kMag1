<?php 
/************************************************************************/
/* Project ArcaWeb                               		   			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
head("Gestione picking");

print("<center>\n<img src=\"logo.jpg\"/>\n<br/>&nbsp;<br/>\n");
print("<form name=\"nome\" action=\"listaart.php\" method=\"get\">\n");
print("<select name=\"reparto\">\n");
reparti();
print("</select>\n");
print("<input type=\"submit\" value=\"Entra\">\n");
print("</form>\n");
print("</center>\n");

print ("<br/><a href=\"index.php\"><img noborder src=\"b_home.gif\"/>Menu principale</a>\n");
footer();
?>