<?php

/************************************************************************/
/* Project ArcaWeb                               		     			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
echo "<html lang=\"it\">\n<head>\n";
echo "<title>Krona Koblenz - Gestione barcode</title>\n";
echo "<meta name=\"author\" content=\"Roberto Ceccarelli\" />\n";
echo "<meta name=\"generator\" content=\"Project ArcaWeb\" />\n";
echo "<link rel=\"author\" type=\"text/html\" href=\"http://strawberryfield.altervista.org/roberto.php\" title=\"Roberto Ceccarelli - Conoscere l'autore del software.\" />\n";
echo "<link rel=\"shortcut icon\" href=\"../favicon.ico\" type=\"image/x-icon\"/>\n";
echo "<link rel=\"icon\" href=\"../favicon.ico\" type=\"image/x-icon\"/>\n";

echo "<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\">\n";
echo "</head>\n";
?>

<body>
    <center>
        <a href="src/index.php"><img src="./logo.jpg" />
            <h2>k-Mag</h2>
            <h4>Clicca per iniziare</h4>
        </a>
    </center>
    <div class="footmsg">
        <center>
            <hr size="1">
            &copy; 2010- <?php print(current_year()); ?> Krona Koblenz Spa
            <br />
        </center>
    </div>

</body>

</html>

<?php
// Calcolo dell'anno corrente
function current_year()
{
    return date("Y");
}
?>