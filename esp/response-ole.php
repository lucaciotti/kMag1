<?php

/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2010 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include 'header.php';

$articolo = strtoupper($_GET['articolo']);
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$anno = current_year();
$db = getODBCSocket();
$Query = "Select descrizion, ubicazione, listino1 from magart where codice = '".$articolo."'";
$descrizion = 'descrizion';

if (!$db->Execute($Query)) {
    print "<p> 01 - C'é un errore: ".$db->errorMsg().'<p>';
}
if ($db->EOF) {
    head($articolo.' - Non trovato');
    print('<h2>Articolo '.$articolo.' non trovato</h2>');
} else {
    //print ("<tr><th>$articolo</th><th>" . trim($db->getfield(descrizion)) . "</th></tr>");

    head($articolo.' - '.trim($db->getfield($descrizion)));
    print('<table border="1">');
    print("<tr><th>$articolo</th><th>".trim($db->getfield($descrizion)).'</th></tr>');
    print('</table>');
    print('<table border="1">');
    print('<tr><th>Mag.</th><th>Giac.</th><th>Disp.</th></tr>');

    // Estraiamo da maggiac le righe per cui l'articolo risulta movimentato nell'anno
    $db2 = getODBCSocket();
    $Query = "Select giacini+progqtacar-progqtasca as giacenza, magazzino from maggiac where esercizio= '".$anno."' and articolo = '".$articolo."'";
    if (!$db2->Execute($Query)) {
        print "<p> 02 - C'è un errore: ".$db->errorMsg().'<p>';
    }
    $giac = 0;

    $db3 = getODBCSocket();
    while (!$db2->EOF) {
        $giacenza = 'giacenza';
        $giac = $db2->getfield($giacenza);
        $maga = $db2->getfield('magazzino');
        print("<tr><td>$maga</td><td>$giac</td>");

        $Query = "Select ordinato-impegnato as dispon from magoi where magazzino='".$maga."' and articolo = '".$articolo."'";
        if (!$db3->Execute($Query)) {
            print "<p> 03 - C'� un errore: ".$db->errorMsg().'<p>';
        }
        $disp = 0;
        while (!$db3->EOF) {
            $disp = $db3->getfield(dispon);
            $db3->MoveNext();
        }
        $disp = $disp + $giac;
        print("<td>$disp</td></tr>");
        $db2->MoveNext();
    }
    print('</table>');

    // Adesso stampiamo tutte le ubicazioni
    print('<table border="1">');
    print('<tr><td>Ubicazione</td><td>'.$db->getfield(ubicazione).'</td></tr>');
    $db4 = getODBCSocket();
    $Query = "Select ubicazione from u_ubicaz where codicearti='".$articolo."'";
    if (!$db4->Execute($Query)) {
        print "<p> 03 - C'� un errore: ".$db->errorMsg().'<p>';
    }
    print('<tr><td>');
    while (!$db4->EOF) {
        print($db4->getfield(ubicazione).'&nbsp;');
        $db4->MoveNext();
    }
    print('</td></tr></table>');
}
print('<br/><a href="ask.php">Altra ricerca</a>');

footer();
