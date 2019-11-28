<?php 
	error_reporting(-1);

    ini_set("display_errors", 1); 
	
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
		
		$colunas=array("ID_DOACAO","QUANTIDADE","DATA_DOACAO","LOGIN", "TIPO");
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
		$selected_tipo = $linha["ID_TIPO"];
		$disabled=true;
		
		$action = "altera.php?tabela=doacao";
	}
	
	else{
		$disabled = false;
		$action = "insere.php?tabela=doacao";
		$value_id_doacao = null;
		$value_quantidade = null;
		$selected_tipo = null;
		
		date_default_timezone_set('America/Sao_Paulo');
		$value_data_doacao = date('d/m/Y H:i:s');
		$selected_id_login = $_SESSION["login"]["id"];
	}
	
	//////////////		//////////////////		//////////////
	$select = "SELECT ID_TIPO AS value, TIPO_DOACAO AS texto FROM TIPO ORDER BY TIPO_DOACAO";
	
	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$tipo[] = $linha;
	}	
	
    /////////////////////////////////////////////////////////////////////////
	
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
	
	$v = array("type"=>"hidden","name"=>"DATA_DOACAO","value"=>$value_data_doacao);
	$f->add_input($v);
	
	$v = array("type"=>"hidden", "name"=>"ID_LOGIN", "value"=>$selected_id_login);
	$f->add_input($v);
	
	$v = array("name"=>"TIPO","selected"=>$selected_tipo);
    $f->add_select($v, $tipo);
	
	$v = array("type"=>"button","texto"=>"ENVIAR");
	$f->add_button($v);	
?>

<h3>Inserir Doação</h3>
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
			data: {tabela: "DOACAO"},
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
				tabela: "POSTAGEM"
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
								0:{0:"DOACAO",1:"LOGIN"},
								1:{0:"DOACAO",1:"TIPO"}
							},
					colunas:{0:"ID_DOACAO",1:"DESCRICAO",2:"QUANTIDADE", 3:"DATA_DOACAO", 4:"LOGIN.NOME AS LOGIN", 5:"TIPO.DESCRICAO AS TIPO"},
					pagina: b
				  },
			success: function(matriz){
				
				$("tbody").html("");
				for(i=0;i<matriz.length;i++){
					tr = "<tr>";
					tr += "<td>"+matriz[i].ID_DOACAO+"</td>";
					tr += "<td>"+matriz[i].LOGIN+"</td>";
					tr += "<td>"+matriz[i].DESCRICAO+"</td>";
					tr += "<td>"+matriz[i].TIPO+"</td>";
					tr += "<td>"+matriz[i].QUANTIDADE+"</td>";
					tr += "<td>"+matriz[i].DATA_DOACAO+"</td>";
					
					
					//if ($.session.get('login')('id') == matriz[i].ID_LOGIN){
						tr += "<td><button value='"+matriz[i].ID_POSTAGEM+"' class='remover'>Remover</button>";
					tr += "<button value='"+matriz[i].ID_DOACAO+"' class='alterar'>Alterar</button></td>";
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
			data: {id: id_alterar, tabela: "POSTAGEM"},
			success: function(dados){
				$("input[name='ID_DOACAO']").val(dados.ID_DOACAO);
				$("input[name='DESCRICAO']").val(dados.DESCRICAO);
				$("input[name='QUANTIDADE']").val(dados.QUANTIDADE);
				$("input[name='TIPO']").val(dados.TIPO);
				$("input[name='DATA_DOACAO']").val(dados.DATA_DOACAO);
				$("input[name='ID_LOGIN']").val(dados.ID_LOGIN);
				$(".cadastrar").attr("class","alterando");
				$(".alterando").html("ALTERAR");
			}
		});
	});
		
		$(document).on("click",".alterando",function(){
			
			$.ajax({
				url:"altera.php?tabela=POSTAGEM",
				type: "post",
				data: {
					ID_DOACAO: $("input[name='ID_DOACAO']").val(),
					DESCRICAO: $("input[name='DESCRICAO']").val(),
					QUANTIDADE: $("input[name='TIPO']").val(),
					QUANTIDADE: $("input[name='QUANTIDADE']").val(),
					DATA_DOACAO: $("input[name='DATA_DOACAO']").val(),
					ID_LOGIN: $("input[name='ID_LOGIN']").val()
				 },
				beforeSend:function(){
					$("button").attr("disabled",true);
				},
				success: function(d){
					$("button").attr("disabled",false);
					if(d=='1'){
						$("#status").html("Postagem Alterada com sucesso!");
						$("#status").css("color","green");
						$(".alterando").attr("class","cadastrar");
						$(".cadastrar").html("CADASTRAR");
						$("input[name='ID_DOACAO']").val("");
						$("input[name='DESCRICAO']").val("");
						$("input[name='TIPO']").val("");
						$("input[name='QUANTIDADE']").val("");
						$("input[name='DATA_DOACAO']").val("");
						$("input[name='ID_LOGIN']").val("");
						
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Doação Não Alterada! Código já existe!");
						$("#status").css("color","red");
					}
				}
			});
		});
		
		//defina a seguinte regra para o botao de envio
		$(document).on("click",".cadastrar",function(){
		
		$.ajax({
			url: "insere.php?tabela=POSTAGEM",
			type: "post",
			data: {
					ID_DOACAO: $("input[name='ID_DOACAO']").val(),
					DESCRICAO: $("input[name='DESCRICAO']").val(),
					QUANTIDADE: $("input[name='TIPO']").val(),
					QUANTIDADE: $("input[name='QUANTIDADE']").val(),
					DATA_DOACAO: $("input[name='DATA_DOACAO']").val(),
					ID_LOGIN: $("input[name='ID_LOGIN']").val()
				 },
			beforeSend:function(){
				$("button").attr("disabled",true);
			},
			success: function(d){
				$("button").attr("disabled",false);
				if(d=='1'){
					$("#status").html("Doação inserida com sucesso!");
					$("#status").css("color","green");
					carrega_botoes();
					paginacao(pagina_atual);
				}
				else{
					console.log(d);
					$("#status").html("Doação Não Inserida! Código já existe!");
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
