<?php

	include("conexao.php");
	include("classeControllerBD.php");
	$ctrl = New ControllerBD($conexao);
	
	
	$ctrl->remover($_POST["id"],$_POST["tabela"]);
	echo "1";
?>
