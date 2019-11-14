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
		
		$colunas=array("ID_COMENTARIO","TEXTO","DATA_COMENTARIO",/*"IMAGEM",*/"ID_POSTAGEM");
		$tabelas[0][0]="comentario";
		$tabelas[0][1]=null;
		$ordenacao = null;
		$condicao = $_POST["id"];
		
		$stmt = $c->selecionar($colunas,$tabelas,$ordenacao,$condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$value_id_comentario = $linha["ID_COMENTARIO"];
		$value_texto = $linha["TEXTO"];
		$value_data_comentario = $linha["DATA_COMENTARIO"];
		//$value_imagem = $linha["IMAGEM"];
		$selected_id_postagem = $linha["ID_POSTAGEM"];
		$disabled=true;
		
		$action = "altera.php?tabela=COMENTARIO";
	}
	
	else{
		$disabled = false;
		$action = "insere.php?tabela=COMENTARIO";
		$value_id_comentario = null;
		$value_texto = null;
		$value_data_comentario = null;
		//$value_imagem = null;
		$selected_id_postagem = null;
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

	$v = array("action"=>"insere.php?tabela=comentario","method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"text","name"=>"ID_COMENTARIO","placeholder"=>"ID DO COMENTARIO", "value"=>$value_id_comentario,"disabled"=>$disabled);
	$f->add_input($v);
	
	if($disabled){
		$v = array("type"=>"hidden","name"=>"ID_COMENTARIO","value"=>$value_id_comentario);
		$f->add_input($v);
	}
	
	$v = array("type"=>"textarea","name"=>"TEXTO","placeholder"=>"TEXTO...", "value"=>$value_texto);
	$f->add_input($v);
	
	$v = array("type"=>"date","name"=>"DATA", "value"=>$value_data_comentario);
	$f->add_input($v);
	
	//$v = array("type"=>"","name"=>"IMAGEM","value"=>$value_imagem);
	//$f->add_input($v);
	
	$v = array("name"=>"ID_POSTAGEM", "selected"=>$selected_id_postagem);
	$f->add_select($v,$matriz);
	
	$v = array("type"=>"button","texto"=>"ENVIAR");
	$f->add_button($v);	
?>

		<h3>Formulário - Inserir Comentário</h3>
		<div id="status"></div>

		<hr />
		<?php
			$f->exibe();
		?>
		
		<script>
			$(function(){
				$("button").click(function(){
					$.ajax ({
						url: "insere.php?tabela=COMENTARIO",
						type: "post",
						data: {
							ID_COMENTARIO: $("input[name='ID_COMENTARIO']").val(),
							TEXTO: $("input[name='TEXTO']").val(),
							DATA_COMENTARIO: $("input[name='DATA_COMENTARIO']").val(),
							//IMAGEM: $("select[name='IMAGEM']").val(),
							ID_POSTAGEM: $("select[name='ID_POSTAGEM']").val()
						},
						beforeSend:function(){
							$("button").attr("disabled", true);
						},
						success: function(d){
							
							$("button").attr("disabled", false);
							if (d=='1') {
								$("#status").html("Comentário adicionado com sucesso!");
								$("#status").css("color","blue");
							} else {
								console.log(d);
								$("#status").html("Comentário não adicionado!");
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