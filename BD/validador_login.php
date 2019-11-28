<?php
	include("conexao.php");
	include("classeControllerBD.php");
	
	$c = new ControllerBD($conexao);
	
	$colunas = array("ID_PERMISSAO, ID_LOGIN");
	$tabelas[0][0] = "LOGIN";
	$tabelas[0][1] = null;//"PERMISSAO";
	
	$condicoes[0]["coluna"]="EMAIL";
	$condicoes[0]["valor"]= $_POST["LOGIN"];
	$condicoes[1]["coluna"]="SENHA";
	$condicoes[1]["valor"]= $_POST["SENHA"];
	
	$r = $c->selecionar($colunas,$tabelas,null,$condicoes,null);
	
	$linha = $r->fetch(PDO::FETCH_ASSOC);
	////print_r($linha);
	
	
	
	if($linha!=null){
		session_start(); //a sessao já comeca em verificacao
		$_SESSION["login"]["permissao"] = $linha["ID_PERMISSAO"];
		$_SESSION["login"]["id"] = $linha["ID_LOGIN"];
		//print_r($_SESSION["login"]["permissao"]);
		

		header("location: index.php");
	}
	else{
		header("location: form_login.php");
	}
?>  
