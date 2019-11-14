<?php

	include("../classeLayout/classeCabecalhoHTML.php");
	include("cabecalho.php");
	
	require_once("../classeForm/InterfaceExibicao.php");
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeOption.php");
	require_once("../classeForm/classeSelect.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");

	include("conexao.php");
	
	//////////////		//////////////////		//////////////////
	
	if (isset($_POST["id"])) {
		require_once("classeControllerBD.php");
		
		$c = new ControllerBD($conexao);
		
		$colunas=array("ID_POSTAGEM","TEXTO","DATA_POSTAGEM",/*"IMAGEM",*/"ID_LOGIN");
		$tabelas[0][0]="postagem";
		$tabelas[0][1]=null;
		$ordenacao = null;
		$condicao = $_POST["id"];
		
		$stmt = $c->selecionar($colunas,$tabelas,$ordenacao,$condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$value_id_postagem = $linha["ID_POSTAGEM"];
		$value_texto = $linha["TEXTO"];
		$value_data_postagem = $linha["DATA_POSTAGEM"];
		//$value_imagem = $linha["IMAGEM"];
		$selected_id_login = $linha["ID_LOGIN"];
		$disabled=true;
		
		$action = "altera.php?tabela=postagem";
	}
	
	else{
		$disabled = false;
		$action = "insere.php?tabela=postagem";
		$value_id_postagem = null;
		$value_texto = null;
		$value_data_postagem = null;
		//$value_imagem = null;
		$selected_id_login = null;
	}
	
	/////////////////		///////////////		///////////////
	
	//seleção dos valores que irão criar o <select>//////
	$select = "SELECT ID_LOGIN AS value, NOME AS texto FROM LOGIN ORDER BY NOME";

	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$matriz[] = $linha;
	} 

	//////////////		//////////////////		//////////////

	$v = array("action"=>"insere.php?tabela=postagem","method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"text","name"=>"ID_POSTAGEM","placeholder"=>"ID DA POSTAGEM", "value"=>$value_id_postagem,"disabled"=>$disabled);
	$f->add_input($v);
	
	if($disabled){
		$v = array("type"=>"hidden","name"=>"ID_POSTAGEM","value"=>$value_id_postagem);
		$f->add_input($v);
	}
	
	$v = array("type"=>"textarea","name"=>"TEXTO","placeholder"=>"TEXTO...", "value"=>$value_texto);
	$f->add_input($v);
	
	$v = array("type"=>"date","name"=>"DATA", "value"=>$value_data_postagem);
	$f->add_input($v);
	
	//$v = array("type"=>"","name"=>"IMAGEM","value"=>$value_imagem);
	//$f->add_input($v);
	
	$v = array("name"=>"ID_LOGIN", "selected"=>$selected_id_login);
	$f->add_select($v,$matriz);
	
	$v = array("type"=>"button","texto"=>"ENVIAR");
	$f->add_button($v);	
?>

		<h3>Formulário - Inserir Postagem</h3>
		<div id="status"></div>

		<hr />
		<?php
			$f->exibe();
		?>
		
		<script>
			$(function(){
				$("button").click(function(){
					$.ajax ({
						url: "insere.php?tabela=postagem",
						type: "post",
						data: {
							ID_POSTAGEM: $("input[name='ID_POSTAGEM']").val(),
							TEXTO: $("input[name='TEXTO']").val(),
							DATA_POSTAGEM: $("input[name='DATA_POSTAGEM']").val(),
							//IMAGEM: $("select[name='IMAGEM']").val(),
							ID_LOGIN: $("select[name='ID_LOGIN']").val()
						},
						beforeSend:function(){
							$("button").attr("disabled", true);
						},
						success: function(d){
							
							$("button").attr("disabled", false);
							if (d=='1') {
								$("#status").html("Postagem adicionada com sucesso!");
								$("#status").css("color","blue");
							} else {
								$("#status").html("Postagem não adicionada!");
								$("#status").css("color","red");
							}
						}
					});
				});
			});
		</script>
	</body>
</html>
</html>