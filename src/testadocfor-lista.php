<?php 
/************************************************************************/
/* Project ArcaWeb                               		   			    */
/* ===========================                               	        */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

/* lettura parametri */ 
$codfor = $_GET['codfor'];
$user = $_GET['user'];
$mode = $_GET['mode'];

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);


$Query = "SELECT * FROM Anagrafe where codice = '" . $codfor . "'" ;
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";

head($tipodoc . $numerodoc . trim($db->getField(codice)) );
print("<table border='1' width='400px'>\n");
print ("<tr><td><strong>Cliente " ."</strong></td><td>" . trim($db->getField(descrizion)) . " (" . $db->getField(codice) . ")</td></tr>");
print ("<tr><td><strong>Indirizzo </strong></td><td> " . trim($db->getField(indirizzo)) . "</td></tr>");
print ("<tr><td><strong>Localit&agrave</strong></td><td> " . trim($db->getField(cap)) . " " . trim($db->getField(localita)) . " (" . trim($db->getField(prov)) . ")" . "</td></tr>");
print ("</table>\n<br>\n");

//inserisco una combobox con l'elenco dei documenti che sono nel picking per il fornitore selezionato.
$Query = "SELECT DISTINCT id_testa, codicecf, datadoc, tipodoc, numerodoc FROM u_picklist ORDER BY Datadoc DESC WHERE codicecf ='" . $codfor . "'" ;
//print($Query);
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>";
if($db->EOF){
    print("<label>Non ci sono documenti per il fornitore selezionato</label>");
}
else{
    print("<form name='input' action='testadoc-lista.php' method='get' >");
    print("<select name='id_testa' id='id_testa' autofocus onchange='setIDTesta(this);'>");
    print("<option value='0'></option>");
    while (!$db->EOF){
        print("<option value='" . $db->getField(id_testa) . "'>");
        print($db->getField(id_testa) .  " - " . $db->getField(tipodoc) . " - " . $db->getField(numerodoc) . " del " . $db->getField(datadoc));
        print("</option>");
        $db->MoveNext();
    }
    print("</select>");
    print("<input type='hidden' name='id' id='id' />");
    print("<input type='hidden' name='user' id='user' />");
    print("<input type='hidden' name='mode' id='mode' value='b' />");

    //questo parametro mi serve in seguito per sapere dove devo tornare quando clicco sul pulsante altra ricerca
    print("<input type='hidden' name='doc' id='doc' value='1' />");
    print("</br>");
    print("<input type='submit' id='btnok' value='OK' />");
    print("</form>");
}

footer();
?>

<script>
    function setIDTesta(sel){
        //alert(sel.options[sel.selectedIndex].value);
        document.getElementById("id").value = sel.options[sel.selectedIndex].value;
    }
</script>