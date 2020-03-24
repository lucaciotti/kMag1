<?php
/************************************************************************/
/* Project ArcaWeb                               		       		    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
$libs = <<<EOT
<script type="text/javascript" src="../jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../jquery.ui.core.min.js"></script>
<script type="text/javascript" src="../jquery.ui.widget.min.js"></script>
EOT;
baseHead("Krona Koblenz Spa - Gestione barcode",false,"$libs");

head("Krona Koblenz Spa - Gestione barcode");

print('<center><img src="logo.jpg"/><br/>&nbsp;<br/>');
?>
<h2>Funzioni per PC</h2></center>

<div id="funzionipc">

	<ul>
	<li><h4><img src="b_props.gif" />&nbsp;<a href="index-pl.php">Menù Packing List</a></h4></li>
	

	<h4>Funzione Ricevimento Merci</h4>
	
	<li><img src="../arca.gif" />&nbsp;<a href="gestione.php">Gestione documenti prelevabili</a></li>
	<li><img src="../arca.gif" />&nbsp;<a href="sparati.php">Elenco righe acquisite</a></li>
	
	<h4>Funzione INVENTARIO</h4>
	
	<li><img src="b_props.gif" />&nbsp;<a href="invTable.php">Riepilogo Sparate Inventario (Richiede Login)</a></li>
	<li><img src="../arca.gif" />&nbsp;<a href="inv_xls.php">Carico inventario da Excel</a></li>
	
	<hr/>
	
	<li><img src="b_home.gif" />&nbsp;<a href="index.php">Ritorna al menu principale</a></li>
	</ul>
</div>

<?php
footer();
?>