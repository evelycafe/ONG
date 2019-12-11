<?php error_reporting(-1);

    ini_set("display_errors", 1); 
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");
	require_once("../classeForm/classeSelect.php");
	require_once("../classeForm/classeOption.php");
	

	if(isset($_POST["id"])){
		require_once("classeControllerBD.php");
		require_once("conexao.php");

		$c = new ControllerBD($conexao);
		$colunas = array("ID_ANIMAL", "NOME", "IDADE", "OBSERVACAO", "ID_LOGIN", "ID_RACA", "DATA_ADOCAO");
		$tabelas[0][0] = "ANIMAL";
		$tabelas[0][1] = "RACA";
		$tabelas[0][1] = "ANIMAL";
		$tabelas[0][1] = "LOGIN";
	
		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_id_animal = $linha["ID_ANIMAL"];
        $value_nome = $linha["NOME"];
        $value_idade = $linha["IDADE"];
        $value_observacao = $linha["OBSERVACAO"];
        $selected_id_login = $linha["ID_LOGIN"];
        $selected_id_raca = $linha["ID_RACA"];
        $data_adocao = $linha["DATA_ADOCAO"];
        $action = "altera.php?tabela=animal";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=animal";
		$value_id_animal = null;
        $value_nome = null;
        $value_idade = null;
        $value_observacao = null;
        $selected_id_login = null;;
        $selected_id_raca = null;
        $data_adocao = null;
	}
	
    /////////////////////////////////////////////////////////////////////////
	
    $select = "SELECT ID_RACA AS value, NOME AS texto FROM RACA ORDER BY NOME";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$raca[] = $linha;
	}	
	
    /////////////////////		/////////////////////////		/////////////////
    
    $select = "SELECT ID_LOGIN AS value, NOME AS texto FROM LOGIN ORDER BY NOME";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$login[] = $linha;
	}	
	
    /////////////////////		/////////////////////////		/////////////////

	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_ANIMAL","placeholder"=>"ID DO ANIMAL...", "value"=>$value_id_animal);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_ANIMAL", "value"=>$value_id_animal);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"text","name"=>"NOME","placeholder"=>"NOME DO ANIMAL...", "value"=>$value_nome);
    $f->add_input($v);

    $v = array("type"=>"number","name"=>"IDADE","placeholder"=>"IDADE...","value"=>$value_idade);
    $f->add_input($v);
	
    $v = array("type"=>"text","name"=>"OBSERVACAO","placeholder"=>"OBSERVAÇÃO...","value"=>$value_observacao);
    $f->add_input($v);
	
    $v = array("name"=>"ID_LOGIN", "selected"=>$selected_id_login);
	$f->add_select($v, $login);
	
	$v = array("name"=>"ID_RACA","selected"=>$selected_id_raca);
    $f->add_select($v,$raca );
    
    $v = array("type"=>"date","name"=>"DATA_ADOCAO","placeholder"=>"DATA DE ADOCAO...","value"=>$data_adocao);
    $f->add_input($v);
	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

	<h3>Formulário - Inserir Animal</h3>
	<div id="status"></div>

	<hr />
	<?php
		$f->exibe();
	?>
	
	<script>
	<?php 
		// permissao:
		// 1: root
		// 2: veterinario
		// 3: usr
		if($_SESSION["login"]["permissao"] == 1){
			echo "permissao=1;";
		}
		else if($_SESSION["login"]["permissao"] == 2){
			echo "permissao=2;";
		}
		else{
			echo "permissao=3;";
		}
	?>
	pagina_atual = 1;
		//quando o documento estiver pronto...
		$(function(){
			
			carrega_botoes();
			
			function carrega_botoes(){
				
				$.ajax({
					url: "quantidade_botoes.php",
					type: "post",
					data: {tabela: "ANIMAL"},
					success: function(q){
						//console.log(q);
						$("#botoes").html("");
						for(i=1;i<=q;i++){
							botao = " <button type='button' class='pg'>" + i + "</button>";
							$("#botoes").append(botao);
						}
					}
				});
			}

			$(document).on("click", ".remover", function(){
				id_remover = $(this).val();

				$.ajax({
					url: "remover.php",
					type: "post",
					data: {
						id: id_remover,
						tabela: "ANIMAL"
					},
					success: function(d){
						if(d == 1){
							$("#status").html("Removido com sucesso");
							carrega_botoes();
							qtd = $("tbody tr").length;
							if(qtd == "1"){
								pagina_atual--;
							}
							paginacao(pagina_atual);
						}
						else if(d == '0'){
							$('#status').html("Você não tem permissão para remover.")
						}
						else if(d == "-1"){
							$('#status').html("Você não está logado.")
						}
					}
				});
			});
			
			$(document).on("click",".pg",function(){
				valor_botao = $(this).html();
				pagina_atual = valor_botao;
				paginacao(valor_botao);
			});
			
			function paginacao(b){
				
				$.ajax({
					url: "carrega_dados.php",
					type: "post",
					data: {
							tabelas: {
								0:{0:"ANIMAL", 1:"RACA"},
								1:{0:"ANIMAL", 1:"LOGIN"}
								},	
							colunas:{0:"ID_ANIMAL",1:"ANIMAL.NOME as NOME",2:"IDADE",3:"OBSERVACAO",
									4:"LOGIN.NOME as LOGIN",5:"RACA.NOME AS RACA", 6:"DATA_ADOCAO"},
							pagina: b
						  },
					success: function(matriz){
						console.log(matriz);
						$("tbody").html("");
						for(i=0;i<matriz.length;i++){
							tr = "<tr>";
							tr += "<td>"+matriz[i].ID_ANIMAL+"</td>";
							tr += "<td>"+matriz[i].NOME+"</td>";
							tr += "<td>"+matriz[i].IDADE+"</td>";
							tr += "<td>"+matriz[i].OBSERVACAO+"</td>";
							tr += "<td>"+matriz[i].RACA+"</td>";
							tr += "<td>"+matriz[i].LOGIN+"</td>";
							tr += "<td>"+matriz[i].DATA_ADOCAO+"</td>";
							tr += "<td><button value='"+matriz[i].ID_ANIMAL+"' class='remover'>Remover</button>";
							tr += "<button value='"+matriz[i].ID_ANIMAL+"' class='alterar'>Alterar</button></td>";
							tr += "</tr>";	
							
							$("tbody").append(tr);
						}
					}
				});
			}
			
			$(document).on("click",".alterar",function(){
			//$(".alterar").click(function(){ 
				id_alterar = $(this).val();			
				$.ajax({
					url: "get_dados_form.php",
					type: "post",
					data: {id: id_alterar, tabela: "ANIMAL"},
					success: function(dados){
						$("input[name='ID_ANIMAL']").val(dados.ID_ANIMAL);
						$("input[name='NOME']").val(dados.NOME);
						$("input[name='IDADE']").val(dados.IDADE);
						$("input[name='OBSERVACAO']").val(dados.OBSERVACAO);
						$("select[name='ID_LOGIN']").val(dados.ID_LOGIN);
						$("select[name='ID_RACA']").val(dados.ID_RACA);
						$("input[name='DATA_ADOCAO']").val(dados.DATA_ADOCAO);
						$(".cadastrar").attr("class","alterando");
						$(".alterando").html("ALTERAR");
					}
				});
			});
				
				$(document).on("click",".alterando",function(){
					
					$.ajax({
						url:"altera.php?tabela=ANIMAL",
						type: "post",
						data: {
							ID_ANIMAL: $("input[name='ID_ANIMAL']").val(),
							NOME: $("input[name='NOME']").val(),
							IDADE: $("input[name='IDADE']").val(),
							OBSERVACAO: $("input[name='OBSERVACAO']").val(),
							ID_LOGIN: $("select[name='ID_LOGIN']").val(),
							ID_RACA: $("select[name='ID_RACA']").val(),
							DATA_ADOCAO: $("input[name='DATA_ADOCAO']").val()
						 },
						beforeSend:function(){
							$("button").attr("disabled",true);
						},
						success: function(d){
							$("button").attr("disabled",false);
							if(d=='1'){
								$("#status").html("Animal Alterado com sucesso!");
								$("#status").css("color","green");
								$(".alterando").attr("class","cadastrar");
								$(".cadastrar").html("CADASTRAR");
								$("input[name='ID_ANIMAL']").val("");
								$("input[name='NOME']").val("");
								$("input[name='IDADE']").val("");
								$("input[name='OBSERVACAO']").val("");
								$("select[name='ID_LOGIN']").val("");
								$("select[name='ID_RACA']").val("");
								$("input[name='DATA_ADOCAO']").val("");
								paginacao(pagina_atual);
							}
							else{
								console.log(d);
								$("#status").html("Animal Não Alterado! Código já existe!");
								$("#status").css("color","red");
							}
						}
					});
				});
				
				//defina a seguinte regra para o botao de envio
				$(document).on("click",".cadastrar",function(){
				vetor = {};
				vetor["ID_ANIMAL"] = $("input[name='ID_ANIMAL']").val();
				vetor["NOME"] = $("input[name='NOME']").val();
				vetor["IDADE"] =  $("input[name='IDADE']").val();
				vetor["OBSERVACAO"] =  $("input[name='OBSERVACAO']").val();
				vetor["ID_RACA"] =  $("select[name='ID_RACA']").val();
				
				if($("select[name='ID_LOGIN']").val() != ''){
					vetor["ID_LOGIN"] = $("select[name='ID_LOGIN']").val();
				}
				
				if($("input[name='DATA_ADOCAO']").val() != ''){
					vetor["DATA_ADOCAO"] =  $("input[name='DATA_ADOCAO']").val();
				}
				
				$.ajax({
					url: "insere.php?tabela=ANIMAL",
					type: "post",
					data: vetor,
					beforeSend:function(){
						$("button").attr("disabled",true);
					},
					success: function(d){
						console.log(d);
						$("button").attr("disabled",false);
						if(d=='1'){
							$("#status").html("Animal inserido com sucesso!");
							$("#status").css("color","green");
							carrega_botoes();
							paginacao(pagina_atual);
						}
						else{
							console.log(d);
							$("#status").html("Animal Não inserido! Código já existe!");
							$("#status").css("color","red");
						}
					}
				});
			});
			
		});
	</script>
</body>
</html>
