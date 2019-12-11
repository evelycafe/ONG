<?php

	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");
	
	session_start();
	date_default_timezone_set('America/Sao_Paulo');
	$ID = $_POST['ID_POSTAGEM'];
	$login = $_SESSION["login"]['id'];

	$v = array("action"=>"insere.php?tabela=comentario","method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"text","name"=>"ID_COMENTARIO","placeholder"=>"ID DO COMENTARIO");
	$f->add_input($v);

	
	$v = array("type"=>"textarea","name"=>"TEXTO_COMENTARIO","placeholder"=>"TEXTO...");
	$f->add_input($v);
	
	$v = array("type"=>"hidden","name"=>"DATA_COMENTARIO", "value"=>date("Y-m-d"));
	$f->add_input($v);
	
	$v = array("type"=>"hidden", "name"=>"ID_POSTAGEM_COMENTARIO", "value"=>$ID);
	$f->add_input($v);
	
	$v = array("type"=>"hidden", "name"=>"ID_LOGIN_COMENTARIO", "value"=>$login);
	$f->add_input($v);
	
	
	$v = array("type"=>"button","class"=>"cadastra_comentario","texto"=>"ENVIAR");
	$f->add_button($v);	
	
	$html = $f->exibe();
	echo $html;	


/*
margin: 1px, 50px, 1px, 20px
n consigo pegar o id da postagem form_postagem linha 422, value ta la mas vem como nulo
problema do login nulo

se vc for admin seu form cadastro eh diferente


erro de historico

*/
?>
