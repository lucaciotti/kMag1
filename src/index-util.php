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
<h2>Utility</h2></center>

<div id="utility">
<ul>
<li><img src="../download.gif" />&nbsp;<a href="../DataWedge.CAB">Scarica utility pistole</a></li>
<li><img src="../download.gif" />&nbsp;<a href="../ts.apk">Scarica utility Pad</a></li>
<li><img src="../bcreader.gif" />&nbsp;<a href="askid.php">ID Terminale</a></li>
<li><img src="../download.gif" />&nbsp;<a href="../app-release_KKE.apk">Scarica ARCA APP per ANDROID</a></li>
<hr/>
<li><img src="b_home.gif" />&nbsp;<a href="index.php">Ritorna al menu principale</a></li>
</ul>
</div>

<?php
footer();
?>