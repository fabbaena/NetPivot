<?php
	require "StartSession.php";
	
	$sesion = new StartSession();
	$usuario = $sesion->get("usuario");	
	if( $usuario == false )
	{	
		header("Location:../index.php");
	}
	else 
	{
		$usuario = $sesion->get("usuario");	
		$sesion->termina_sesion();	
		header("location:../index.php");
	}
?>