<?php
/************************************************************************/
/* Project ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
//include("odbcSocketLib.php");
head("test");

checkPermission();

$idCart = trim($_GET['id']);
$ubicaz = trim($_GET['ubi']);

$maga="00".substr($idCart,4,3);
$numCart=intval(substr($idCart,7,5));

$db = getODBCSocket();
$notify = 0;

$Query = "DELETE FROM u_invent WHERE codcart=='$idCart'";
if (!$db->Execute($Query)) {
    print($db->errorMsg() . "$Query<br>");
    return -1;
}

$notify = $notify +1;

$db = null;

print($notify);
header("location: invTable.php?maga=$maga&num=$numCart&ubi=$ubicaz");
?>