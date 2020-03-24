<?php
/************************************************************************/
/* Project ArcaWeb                               		       		    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
//head("Krona Koblenz Spa - Manutenzione PL");
head_jquery_pc("Krona Koblenz Spa - Manutenzione PL",$script);

print('<center><img src="logo.jpg"/><br/>&nbsp;<br/>');
?>
<h2>Funzioni disponibili</h2></center>
<ul>
<li><img src="b_props.gif" />&nbsp;<a href="pl-ready.php">Distinta Partenza PL</a></li>
<li><img src="b_props.gif" />&nbsp;<a href="ask_pl_edit.php">Dettaglio PL (Richiede Login)</a></li>

<h4>Funzione Spedizione Merci</h4>
<li><img src="../bcreader.gif" />&nbsp;<a href="askpl-banc.php">Gestione bancali</a></li>
<li><img src="../bcreader.gif" />&nbsp;<a href="askpl-banc-rep.php">Riassegnazione bancali per reparto</a></li>

<!--li><img src="../bcreader.gif" />&nbsp;<a href="askdelpl.php">Cancellazione packing list</a></li>
<li><img src="../bcreader.gif" />&nbsp;<a href="askdelrigapl.php">Cancellazione riga packing list</a></li>
<li><img src="../bcreader.gif" />&nbsp;<a href="pl-gestcolli.php">Cancellazione / riapertura colli</a></li-->
<hr/>
<li><img src="b_home.gif" />&nbsp;<a href="index-pc.php">Ritorna al menu precedente</a></li>
</ul>
<?php
footer();
?>