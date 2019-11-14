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
		
		$colunas=array("ID_DOACAO","QUANTIDADE","DATA_DOACAO","ID_LOGIN");
		$tabelas[0][0]="doacao";
		$tabelas[0][1]=null;
		$ordenacao = null;
		$condicao = $_POST["id"];
		
		$stmt = $c->selecionar($colunas,$tabelas,$ordenacao,$condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$value_id_doacao = $linha["ID_DOACAO"];
		$value_quantidade = $linha["QUANTIDADE"];
		$value_data_doacao = $linha["DATA_DOACAO"];
		$selected_id_login = $linha["ID_LOGIN"];
		$disabled=true;
		
		$action = "altera.php?tabela=doacao";
	}
	
	else{
		$disabled = false;
		$action = "insere.php?tabela=doacao";
		$value_id_doacao = null;
		$value_quantidade = null;
		$value_data_doacao = null;
		$selected_id_login = null;
	}
	
	/////////////////		///////////////		///////////////
	
	//seleção dos valores que irão criar o <select>//////
	$select = "SELECT ID_LOGIN AS value, NOME AS texto FROM login ORDER BY NOME";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$matriz[] = $linha;
	} 

	//////////////		//////////////////		//////////////

	$v = array("action"=>"insere.php?tabela=doacao","method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"text","name"=>"ID_DOACAO","placeholder"=>"ID DA DOACAO", "value"=>$value_id_doacao,"disabled"=>$disabled);
	$f->add_input($v);
	
	if($disabled){
		$v = array("type"=>"hidden","name"=>"ID_DOACAO","value"=>$value_id_doacao);
		$f->add_input($v);
	}
	
	$v = array("type"=>"text","name"=>"QUANTIDADE", "value"=>$value_quantidade);
	$f->add_input($v);
	
	$v = array("type"=>"date","name"=>"DATA_DOACAO","value"=>$value_data_doacao);
	$f->add_input($v);
	
	$v = array("name"=>"ID_LOGIN", "selected"=>$selected_id_login);
	$f->add_select($v,$matriz);
	
	$v = array("type"=>"button","texto"=>"ENVIAR");
	$f->add_button($v);	
?>

		<h3>Formulário - Inserir Doação</h3>
		<div id="status"></div>

		<hr />
		<?php
			$f->exibe();
		?>
		
		<script>
			$(function(){
				$("button").click(function(){
					$.ajax ({
						url: "insere.php?tabela=doacao",
						type: "post",
						data: {
							ID_DOACAO: $("input[name='ID_POSTAGEM']").val(),
							QUANTIDADE: $("input[name='TEXTO']").val(),
							DATA_DOACAO: $("select[name='DATA_DOACAO']").val(),
							ID_LOGIN: $("select[name='ID_LOGIN']").val()
						},
						beforeSend:function(){
							$("button").attr("disabled", true);
						},
						success: function(d){
							
							$("button").attr("disabled", false);
							if (d=='1') {
								$("#status").html("Doação adicionada com sucesso!");
								$("#status").css("color","blue");
							} else {
								$("#status").html("Doação não adicionada!");
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