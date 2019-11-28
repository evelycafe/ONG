<?php error_reporting(-1);

    ini_set("display_errors", 1); 
	
	require_once("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
	
	require_once("../classeForm/classeInput.php");
	require_once("../classeForm/classeForm.php");
	require_once("../classeForm/classeButton.php");
	require_once("../classeForm/classeSelect.php");
	require_once("../classeForm/classeOption.php");

	include("conexao.php");
	
	//////////////		//////////////////		//////////////////
	
	if (isset($_POST["id"])) {
		require_once("classeControllerBD.php");
		
		$c = new ControllerBD($conexao);
		
		$colunas=array("ID_COMENTARIO","TEXTO","DATA_COMENTARIO","ID_POSTAGEM");
		$tabelas[0][0]="COMENTARIO";
		$tabelas[0][1]=null;
		$ordenacao = null;
		$condicao = $_POST["id"];
		
		$stmt = $c->selecionar($colunas,$tabelas,$ordenacao,$condicao);
		$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$value_id_comentario = $linha["ID_COMENTARIO"];
		$value_texto = $linha["TEXTO"];
		$value_data_comentario = $linha["DATA_COMENTARIO"];
		$selected_id_postagem = $linha["ID_POSTAGEM"];
		$disabled=true;
		
		$action = "altera.php?tabela=comentario";
	}
	
	else{
		$disabled = false;
		$action = "insere.php?tabela=comentario";
		$value_id_comentario = null;
		$value_texto = null;
		$value_data_comentario = null;
		$selected_id_postagem = null;
	}
	
	/////////////////		///////////////		///////////////
	
	$select = "SELECT ID_POSTAGEM AS value, TEXTO AS texto FROM POSTAGEM ORDER BY TEXTO";

	$stmt = $conexao->prepare($select);
	$stmt->execute();
	
	while($linha=$stmt->fetch()){
		$postagem[] = $linha;
	} 

	//////////////		//////////////////		//////////////

	$v = array("action"=>$action,"method"=>"post");
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
	
	$v = array("name"=>"ID_POSTAGEM", "selected"=>$selected_id_postagem);
	$f->add_select($v,$postagem);
	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"CADASTRAR");
	$f->add_button($v);	
	
?>
<!DOCTYPE>

<h3>Inserir Comentário</h3>
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
			data: {tabela: "COMENTARIO"},
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
				tabela: "COMENTARIO"
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
								0:{0:"COMENTARIO",1:null}
							},
					colunas:{0:"ID_COMENTARIO",1:"TEXTO",2:"DATA_COMENTARIO",3:"ID_POSTAGEM"}, 
					pagina: b
				  },
			success: function(matriz){
				
				$("tbody").html("");
				for(i=0;i<matriz.length;i++){
					tr = "<tr>";
					tr += "<td>"+matriz[i].ID_COMENTARIO+"</td>";
					tr += "<td>"+matriz[i].TEXTO+"</td>";
					tr += "<td>"+matriz[i].DATA_COMENTARIO+"</td>";
					tr += "<td>"+matriz[i].ID_POSTAGEM+"</td>";
					tr += "<td><button value='"+matriz[i].ID_COMENTARIO+"' class='remover'>Remover</button>";
					tr += "<button value='"+matriz[i].ID_COMENTARIO+"' class='alterar'>Alterar</button></td>";
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
			data: {id: id_alterar, tabela: "COMENTARIO"},
			success: function(dados){
				$("input[name='ID_COMENTARIO']").val(dados.ID_COMENTARIO);
				$("input[name='TEXTO']").val(dados.TEXTO);
				$("input[name='DATA_COMENTARIO']").val(dados.DATA_COMENTARIO);
				$("select[name='ID_POSTAGEM']").val(dados.ID_POSTAGEM);
				$(".cadastrar").attr("class","alterando");
				$(".alterando").html("ALTERAR");
			}
		});
	});
		
	$(document).on("click",".alterando",function(){
		$.ajax({
			url:"altera.php?tabela=COMENTARIO",
			type: "post",
			data: {
				ID_COMENTARIO: $("input[name='ID_COMENTARIO']").val(),
				TEXTO: $("input[name='TEXTO']").val(),
				DATA_COMENTARIO: $("input[name='DATA_COMENTARIO']").val(),
				ID_POSTAGEM: $("select[name='ID_POSTAGEM']").val()
			 },
			beforeSend:function(){
				$("button").attr("disabled",true);
			},
			success: function(d){
				$("button").attr("disabled",false);
				if(d=='1'){
					$("#status").html("Comentário Alterado com sucesso!");
					$("#status").css("color","green");
					$(".alterando").attr("class","cadastrar");
					$(".cadastrar").html("CADASTRAR");
					$("input[name='ID_COMENTARIO']").val("");
					$("input[name='TEXTO']").val("");
					$("input[name='DATA_COMENTARIO']").val("");
					$("select[name='ID_POSTAGEM']").val("");
					
					paginacao(pagina_atual);
				}
				else{
					console.log(d);
					$("#status").html("Comentário Não Alterado! Código já existe!");
					$("#status").css("color","red");
				}
			}
		});
	});
	
	$(document).on("click",".cadastrar",function(){
		$.ajax({
			url: "insere.php?tabela=COMENTARIO",
			type: "post",
			data: {
					ID_COMENTARIO: $("input[name='ID_COMENTARIO']").val(),
					TEXTO: $("input[name='TEXTO']").val(),
					DATA_COMENTARIO: $("input[name='DATA_COMENTARIO']").val(),
					ID_POSTAGEM: $("select[name='ID_POSTAGEM']").val()
				 },
			beforeSend:function(){
				$("button").attr("disabled",true);
			},
			success: function(d){
				$("button").attr("disabled",false);
				if(d=='1'){
					$("#status").html("Comentário inserido com sucesso!");
					$("#status").css("color","green");
					carrega_botoes();
					paginacao(pagina_atual);
				}
				else{
					console.log(d);
					$("#status").html("Comentário Não Alterado! Código já existe!");
					$("#status").css("color","red");
				}
			}
		});
	});
});
	</script>
</body>
</html>
