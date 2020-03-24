<?php

include("header.php");
// da questo punto gestione a sessioni
session_start();

$user =  $_POST['codice'];
$pwd =  $_POST['password'];

//connect to database
$USERMAP = array(
	"fabri" => "fibra",
	"ced" => "qazxsw",
	"emanuela" => "manuprio" );

if(array_key_exists($user, $USERMAP)) {
	if ($USERMAP[$user] == $pwd){
		 session_start();
		 $_SESSION["UserPL"] = "$user";
		 Header("Location: index-pl.php");
	} else
	{
		//session_register_shutdown();
		Header("Location: login.php?error=2");
	}
} else
{
	//session_register_shutdown();
	Header("Location: login.php?error=1");
}

?>