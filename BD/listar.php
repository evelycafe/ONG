<?php
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	require_once("../classeLayout/classeTabela.php");
	
	require_once("conexao.php");
	require_once("classeControllerBD.php");
	
	require_once("configuracoes_listar.php");
	
	if($_GET["t"]=="login"){
		require_once("form_login.php");
	}

	else if($_GET["t"]=="veterinario"){
		require_once("form_veterinario.php");
	}

	else if($_GET["t"]=="especie"){
		require_once("form_especie.php");
	}
	
	else if($_GET["t"]=="raca"){
		require_once("form_raca.php");
	}
	
	else if($_GET["t"]=="animal"){
		require_once("form_animal.php");
	}
	
	else if($_GET["t"]=="postagem"){
		require_once("form_postagem.php");
	}
	
	else if($_GET["t"]=="historico_atendimento"){
		require_once("form_historico_atendimento.php");
	}
	
	$c = new ControllerBD($conexao);
	
	$r = $c->selecionar($colunas,$t,null,null," LIMIT 0,5");
	
	while($linha = $r->fetch(PDO::FETCH_ASSOC)){
		$matriz[] = $linha;
	}
	
	$t = new Tabela($matriz,$t[0][0]);
	$t->exibe();
?>