<?php 
	error_reporting(-1);

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
		$colunas = array("ID_CONSULTA", "ID_ANIMAL", "ID_VETERINARIO", "ID_HISTORICO_ATENDIMENTO");
		$tabelas[0][0] = "CONSULTA";
		$tabelas[0][1] = null;

		$ordenacao = null;
		$condicao = $_POST["id"];

		$stmt = $c->selecionar($colunas, $tabelas, $ordenacao, $condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);

		$value_consulta = $linha["ID_CONSULTA"];
		$selected_id_animal = $linha["ID_ANIMAL"];
        $selected_id_vet = $linha["ID_VETERINARIO"];
        $selected_id_historico = $linha["ID_HISTORICO_ATENDIMENTO"];
 
        $action = "altera.php?tabela=consulta";
        $disabled = true;
	}
	else{
        $disabled = false;
		$action = "insere.php?tabela=consulta";
		$value_consulta = null;
		$selected_id_animal = null;
        $selected_id_vet = null;
        $selected_id_historico = null;

	}
	
	/////////////////////		//////////////////////		/////////////
    $select = "SELECT ID_ANIMAL AS value, NOME AS texto FROM ANIMAL ORDER BY NOME";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$animal[] = $linha;
	}	
	
    /////////////////////////////////////////////////////////////////////////
	
    $select = "SELECT ID_VETERINARIO AS value, NOME AS texto FROM VETERINARIO ORDER BY NOME";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$veterinario[] = $linha;
	}	
	
    /////////////////////		/////////////////////////		/////////////////

	 $select = "SELECT ID_HISTORICO_ATENDIMENTO AS value, DATA_ATENDIMENTO AS texto FROM HISTORICO_ATENDIMENTO ORDER BY DATA_ATENDIMENTO";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$historico[] = $linha;
	}	
	
    /////////////////////		/////////////////////////		/////////////////

	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"number","name"=>"ID_CONSULTA","placeholder"=>"ID DA CONSULTA...", "value"=>$value_consulta);
    $f->add_input($v);
    
    if($disabled){
        $v = array("type"=>"hidden", "name"=>"ID_CONSULTA", "value"=>$value_consulta, "disabled"=>$disabled);
        $f->add_Input($v);
    }
	
    $v = array("name"=>"ID_ANIMAL","selected"=>$selected_id_animal);
    $f->add_select($v, $animal);
	
	$v = array("name"=>"ID_VETERINARIO","selected"=>$selected_id_vet);
    $f->add_select($v,$veterinario );
    
    $v = array("name"=>"ID_HISTORICO_ATENDIMENTO","selected"=>$selected_id_historico);
    $f->add_select($v,$historico );
	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
	$f->add_button($v);	
?>
<!DOCTYPE html>

<h3>Inserir Consulta</h3>
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

$(function(){
	
	carrega_botoes();
	
	function carrega_botoes(){
		
		$.ajax({
			url: "quantidade_botoes.php",
			type: "post",
			data: {tabela: "CONSULTA"},
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
				tabela: "CONSULTA"
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
								0:{0:"CONSULTA",1:"ANIMAL"},
								1:{0:"CONSULTA",1:"VETERINARIO"},
								2:{0:"CONSULTA",1:"HISTORICO_ATENDIMENTO"}
							},
					colunas:{0:"ID_CONSULTA", 1:"ID_ANIMAL",2:"ID_VETERINARIO",3:"ID_HISTORICO_ATENDIMENTO"},
					pagina: b
					},
			success: function(matriz){
				
				$("tbody").html("");
				for(i=0;i<matriz.length;i++){
					tr = "<tr>";
					tr += "<td>"+matriz[i].ID_CONSULTA+"</td>";
					tr += "<td>"+matriz[i].ID_ANIMAL+"</td>";
					tr += "<td>"+matriz[i].ID_VETERINARIO+"</td>";
					tr += "<td>"+matriz[i].ID_HISTORICO_ATENDIMENTO+"</td>";
					tr += "<td><button value='"+matriz[i].ID_CONSULTA+"' class='remover'>Remover</button>";
					tr += "<button value='"+matriz[i].ID_CONSULTA+"' class='alterar'>Alterar</button></td>";
					tr += "</tr>";	
					$("tbody").append(tr);
				}
			}
		});
	}
	
	$(document).on("click",".alterar",function(){
		id_alterar = $(this).val();			
		$.ajax({
			url: "get_dados_form.php",
			type: "post",
			data: {id: id_alterar, tabela: "CONSULTA"},
			success: function(dados){
				$("input[name='ID_CONSULTA']").val(dados.ID_CONSULTA);
				$("input[name='ID_ANIMAL']").val(dados.ID_ANIMAL);
				$("input[name='ID_VETERINARIO']").val(dados.ID_VETERINARIO);
				$("input[name='ID_HISTORICO_ATENDIMENTO']").val(dados.ID_HISTORICO_ATENDIMENTO);

				$(".cadastrar").attr("class","alterando");
				$(".alterando").html("ALTERAR");
			}
		});
	});
		
		$(document).on("click",".alterando",function(){
			
			$.ajax({
				url:"altera.php?tabela=CONSULTA",
				type: "post",
				data: {
					ID_CONSULTA: $("input[name='ID_CONSULTA']").val(),
					ID_ANIMAL: $("input[name='ID_ANIMAL']").val(),
					VETERINARIO: $("input[name='ID_VETERINARIO']").val(),
					HISTORICO_ATENDIMENTO: $("input[name='ID_HISTORICO_ATENDIMENTO']").val(),
					
					},
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Consulta Alterada com sucesso!");
						$("#status").css("color","green");
						$(".alterando").attr("class","cadastrar");
						$(".cadastrar").html("CADASTRAR");
						$("input[name='ID_CONSULTA']").val("");
						$("input[name='ID_ANIMAL']").val("");
						$("input[name='ID_VETERINARIO']").val("");
						$("input[name='ID_HISTORICO_ATENDIMENTO']").val("");
														
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Consulta Não Alterada! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
		$(document).on("click",".cadastrar",function(){
		
		$.ajax({
			url: "insere.php?tabela=CONSULTA",
			type: "post",
			data: {
					ID_CONSULTA: $("input[name='ID_CONSULTA']").val(),
					ID_ANIMAL: $("input[name='ID_ANIMAL']").val(),
					VETERINARIO: $("input[name='ID_VETERINARIO']").val(),
					HISTORICO_ATENDIMENTO: $("input[name='ID_HISTORICO_ATENDIMENTO']").val(),
				
					},
			beforeSend:function(){
				$("button").attr("disabled",true);
			},
			success: function(d){
				$("button").attr("disabled",false);
				if(d=='1'){
					$("#status").html("Consulta inserida com sucesso!");
					$("#status").css("color","green");
					carrega_botoes();
					paginacao(pagina_atual);
				}
				else{
					console.log(d);
					$("#status").html("Consulta Não Alterada! Código já existe!");
					$("#status").css("color","red");
				}
			}
		});
	});
	
});
</script>
</body>
</html>
