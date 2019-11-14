<?php
include("conexao.php");

if(!empty($_POST)){

	include("classeControllerBD.php");
	
	$c = new ControllerBD($conexao);
	$c->alterar($_POST,$_GET["tabela"]) or die("0");
	echo "1";	
}

?>