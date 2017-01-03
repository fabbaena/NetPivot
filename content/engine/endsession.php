<?php
	require_once dirname(__FILE__) ."/../model/StartSession.php";
	require_once dirname(__FILE__) .'/../model//UserList.php';
	require_once dirname(__FILE__) ."/../model/Event.php";
	
	$sesion = new StartSession();
	$usuario = $sesion->get("usuario");	
	if( $usuario == false )
	{	
		header("Location:../index.php");
	}
	else 
	{
		$usuario = $sesion->get("usuario");	
		$user = $sesion->get("user");
		$sesion->termina_sesion();	
		new Event($user, "Logged out.", 2);
		header("location:../index.php");
	}
?>