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
		
		$colunas=array("ID_TIPO","TIPO_DOACAO","ID_DOACAO");
		$tabelas[0][0]="tipo";
		$tabelas[0][1]=null;
		$ordenacao = null;
		$condicao = $_POST["id"];
		
		$stmt = $c->selecionar($colunas,$tabelas,$ordenacao,$condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$value_id_tipo = $linha["ID_TIPO"];
		$value_tipo_doacao = $linha["TIPO_DOACAO"];
		$selected_id_doacao = $linha["ID_DOACAO"];
		$disabled=true;
		
		$action = "altera.php?tabela=tipo";
	}
	
	else{
		$disabled = false;
		$action = "insere.php?tabela=tipo";
		$value_id_tipo = null;
		$value_tipo_doacao = null;
		$selected_id_doacao = null;
	}
	
	/////////////////		///////////////		///////////////
	
	//seleção dos valores que irão criar o <select>//////
	$select = "SELECT ID_DOACAO AS value, QUANTIDADE AS texto FROM login ORDER BY DATA_DOACAO";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$matriz[] = $linha;
	} 

	//////////////		//////////////////		//////////////

	$v = array("action"=>"insere.php?tabela=tipo","method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"text","name"=>"ID_TIPO","placeholder"=>"ID DO TIPO DE DOACAO", "value"=>$value_id_tipo,"disabled"=>$disabled);
	$f->add_input($v);
	
	if($disabled){
		$v = array("type"=>"hidden","name"=>"ID_TIPO","value"=>$value_id_tipo);
		$f->add_input($v);
	}
	
	$v = array("type"=>"text","name"=>"TIPO_DOACAO", "value"=>$value_tipo_doacao);
	$f->add_input($v);
	
	$v = array("name"=>"ID_DOACAO", "selected"=>$selected_id_doacao);
	$f->add_select($v,$matriz);
	
	$v = array("type"=>"button","texto"=>"ENVIAR");
	$f->add_button($v);	
?>

		<h3>Formulário - Inserir Tipo de Doação</h3>
		<div id="status"></div>

		<hr />
		<?php
			$f->exibe();
		?>
		
		<script>
			$(function(){
				$("button").click(function(){
					$.ajax ({
						url: "insere.php?tabela=tipo",
						type: "post",
						data: {
							ID_TIPO: $("input[name='ID_TIPO']").val(),
							TIPO_DOACAO: $("input[name='TIPO_DOACAO']").val(),
							ID_DOACAO: $("select[name='ID_DOACAO']").val()
						},
						beforeSend:function(){
							$("button").attr("disabled", true);
						},
						success: function(d){
							
							$("button").attr("disabled", false);
							if (d=='1') {
								$("#status").html("Tipo adicionado com sucesso!");
								$("#status").css("color","blue");
							} else {
								$("#status").html("Tipo não adicionado!");
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