<?php 
/************************************************************************/
/* Project ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2008 by Roberto Ceccarelli                             */
/*                                                                      */
/************************************************************************/

include("header.php");
 
setcookie("locked",$articolo,time()-1000);

head("Sblocco terminale");
Print("<h2>Terminale sbloccato</h2>");
footer();
?>