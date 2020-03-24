<?php 
/************************************************************************/
/* Project ArcaWeb                               		   			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2010 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
head("Gestione picking");

print('<center><img src="logo.jpg"/><br/>&nbsp;<br/>');
print('<form name="nome" action="lista.php" method="get">');
print('<select name="user">');
utenti();
print('</select>');
print('<input type="submit" value="Entra">');
print('</form>');
print('</center>');

footer();
?>