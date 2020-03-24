<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrea
 * Date: 30/05/13
 * Time: 11.37
 * To change this template use File | Settings | File Templates.
 */
include("header.php");

headx("Cancellazione inventario");

$db = getODBCSocket();
//$conn = new COM("ADODB.Connection");
//$conn->Open($connectionstring);
$Query = "DELETE FROM U_INVENT";
if (!$db->Execute($Query)) print "<p> 01 - C'é un errore: " . $db->errorMsg() . "<p>" and die();

print("Cancellazione effettuata correttamente");

goMain();
footer();
?>