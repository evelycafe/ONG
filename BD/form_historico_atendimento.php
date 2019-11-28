<?php 
	error_reporting(-1);

    ini_set("display_errors", 1); 
	
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");

	if(isset($_POST["id"])){
		require_once("classeControllerBD.php");
		require_once("conexao.php");

		$c = new ControllerBD($conexao);
		$colunas = array("ID_HISTORICO_ATENDIMENTO", "DATA_ATENDIMENTO", "MEDICACAO", "OBSERVACAO");
		$tabelas[0][0] = "historico_atendimento";
		$tabelas[0][1] = null;
		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_id_historico = $linha["ID_HISTORICO_ATENDIMENTO"];
        $value_data = $linha["DATA_ATENDIMENTO"];
        $value_medicacao = $linha["MEDICACAO"];
        $value_observacao = $linha["OBSERVACAO"];
        $action = "altera.php?tabela=historico_atendimento";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=historico_atendimento";
		$value_id_historico = null;
        $value_data = null;
        $value_medicacao = null;
        $value_observacao = null;
	}


	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_HISTORICO_ATENDIMENTO","placeholder"=>"ID DO HISTORICO DE ATENDIMENTO...", "value"=>$value_id_historico);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_HISTORICO_ATENDIMENTO", "value"=>$value_id_historico);
        $f->add_Input($v);
    }
	
	$v = array("type"=>"date","name"=>"DATA_ATENDIMENTO","value"=>$value_data);
    $f->add_input($v);

    $v = array("type"=>"text","name"=>"MEDICACAO","placeholder"=>"MEDICAÇÃO...","value"=>$value_medicacao);
    $f->add_input($v);
	
    $v = array("type"=>"text","name"=>"OBSERVACAO","placeholder"=>"OBSERVAÇÃO...","value"=>$value_observacao);
    $f->add_input($v);	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

<h3>Histórico de Atendimento</h3>
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
				data: {tabela: "HISTORICO_ATENDIMENTO"},
				success: function(q){
					console.log(q);
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
					tabela: "HISTORICO_ATENDIMENTO"
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
						tabelas:{
									0:{0:"HISTORICO_ATENDIMENTO",1:null}
								},
						colunas:{0:"ID_HISTORICO_ATENDIMENTO",1:"DATA_ATENDIMENTO",2:"MEDICACAO",3:"OBSERVACAO"}, 
						pagina: b
					  },
				success: function(matriz){
					
					$("tbody").html("");
					for(i=0;i<matriz.length;i++){
						tr = "<tr>";
						tr += "<td>"+matriz[i].ID_HISTORICO_ATENDIMENTO+"</td>";
						tr += "<td>"+matriz[i].DATA_ATENDIMENTO+"</td>";
						tr += "<td>"+matriz[i].MEDICACAO+"</td>";
						tr += "<td>"+matriz[i].OBSERVACAO+"</td>";
						tr += "<td><button value='"+matriz[i].ID_HISTORICO_ATENDIMENTO+"' class='remover'>Remover</button>";
						tr += "<button value='"+matriz[i].ID_HISTORICO_ATENDIMENTO+"' class='alterar'>Alterar</button></td>";
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
				data: {id: id_alterar, tabela: "HISTORICO_ATENDIMENTO"},
				success: function(dados){
					$("input[name='ID_HISTORICO_ATENDIMENTO']").val(dados.ID_HISTORICO_ATENDIMENTO);
					$("input[name='DATA_ATENDIMENTO']").val(dados.DATA_ATENDIMENTO);
					$("input[name='MEDICACAO']").val(dados.MEDICACAO);
					$("input[name='OBSERVACAO']").val(dados.OBSERVACAO);
					$(".cadastrar").attr("class","alterando");
					$(".alterando").html("ALTERAR");
				}
			});
		});
			
			$(document).on("click",".alterando",function(){
				
				$.ajax({
					url:"altera.php?tabela=HISTORICO_ATENDIMENTO",
					type: "post",
					data: {
						ID_HISTORICO_ATENDIMENTO: $("input[name='ID_HISTORICO_ATENDIMENTO']").val(),
						DATA_ATENDIMENTO: $("input[name='DATA_ATENDIMENTO']").val(),
						MEDICACAO: $("input[name='MEDICACAO']").val(),
						OBSERVACAO: $("input[name='OBSERVACAO']").val()
					 },
					beforeSend:function(){
						$("button").attr("disabled",true);
					},
					success: function(d){
						$("button").attr("disabled",false);
						if(d=='1'){
							$("#status").html("Histórico Alterado com sucesso!");
							$("#status").css("color","green");
							$(".alterando").attr("class","cadastrar");
							$(".cadastrar").html("CADASTRAR");
							$("input[name='ID_HISTORICO_ATENDIMENTO']").val("");
							$("input[name='DATA_ATENDIMENTO']").val("");
							$("input[name='MEDICACAO']").val("");
							$("input[name='OBSERVACAO']").val("");
							
							paginacao(pagina_atual);
						}
						else{
							console.log(d);
							$("#status").html("Histórico Não Alterado! Código já existe!");
							$("#status").css("color","red");
						}
					}
				});
			});
			
			//defina a seguinte regra para o botao de envio
			$(document).on("click",".cadastrar",function(){
			
			$.ajax({
				url: "insere.php?tabela=HISTORICO_ATENDIMENTO",
				type: "post",
				data: {
						ID_HISTORICO_ATENDIMENTO: $("input[name='ID_HISTORICO_ATENDIMENTO']").val(),
						DATA_ATENDIMENTO: $("input[name='DATA_ATENDIMENTO']").val(),
						MEDICACAO: $("input[name='MEDICACAO']").val(),
						OBSERVACAO: $("input[name='OBSERVACAO']").val()
					 },
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Histórico inserido com sucesso!");
						$("#status").css("color","green");
						carrega_botoes();
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Histórico Não Alterado! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
	});
</script>
</body>
</html>
