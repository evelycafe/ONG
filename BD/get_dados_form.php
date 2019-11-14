<?php
	header("Content-type: application/json");

	include("conexao.php");
	include("classeControllerBD.php");
	
	$c = new ControllerBD($conexao);

	$id = $_POST["id"];
	$tabela[0][0] = $_POST["tabela"];
	$tabela[0][1] = null;

	$colunas = array("*");

	$r = $c->selecionar($colunas,$tabela,null,$id);
	$linha = $r->fetch(PDO::FETCH_ASSOC);

	echo json_encode($linha);
?>