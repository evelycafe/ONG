<?php

	require_once("../classeLayout/classeCabecalhoHTML.php");
	//require_once("cabecalho.php");

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
		
		$colunas=array("ID_POSTAGEM","TEXTO","DATA_POSTAGEM",/*"IMAGEM",*/"ID_LOGIN");
		$tabelas[0][0]="postagem";
		$tabelas[0][1]="login";
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
		
		date_default_timezone_set('America/Sao_Paulo');
		$value_data_postagem = date("Y-m-d");

		$selected_id_login = $_SESSION["login"]["id"];
	}
	
	/////////////////		///////////////		///////////////


	$v = array("action"=>$action,"method"=>"post");
	$f = new Form($v);
	
	$v = array("type"=>"text","name"=>"ID_POSTAGEM","placeholder"=>"ID DA POSTAGEM", "value"=>$value_id_postagem,"disabled"=>$disabled);
	$f->add_input($v);
	
	if($disabled){
		$v = array("type"=>"hidden","name"=>"ID_POSTAGEM","value"=>$value_id_postagem);
		$f->add_input($v);
	}
	
	$v = array("type"=>"textarea","name"=>"TEXTO","placeholder"=>"TEXTO...", "value"=>$value_texto);
	$f->add_input($v);
	
	$v = array("type"=>"hidden","name"=>"DATA_POSTAGEM", "value"=>$value_data_postagem);
	$f->add_input($v);
	
	//$v = array("type"=>"","name"=>"IMAGEM","value"=>$value_imagem);
	//$f->add_input($v);
	
	$v = array("type"=>"hidden", "name"=>"ID_LOGIN", "value"=>$selected_id_login);
	$f->add_input($v);
	
	
	$v = array("type"=>"button","class"=>"cadastrar","texto"=>"POSTAR");
	$f->add_button($v);	
?>

<h3>Formulário - Inserir Postagem</h3>
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
	paginacao(1);
	
	function carrega_botoes(){
		
		$.ajax({
			url: "quantidade_botoes.php",
			type: "post",
			data: {tabela: "POSTAGEM"},
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
								0:{0:"POSTAGEM",1:"LOGIN"}
							},
					colunas:{0:"ID_POSTAGEM",1:"TEXTO",2:"DATA_POSTAGEM",3:"LOGIN.NOME AS LOGIN", 4:"LOGIN.ID_LOGIN AS ID_LOGIN"},
					pagina: b
				  },
			success: function(matriz){
				
				$("tbody").html("");
				for(i=0;i<matriz.length;i++){
					tr = "<tr>";
					tr += "<td>"+matriz[i].ID_POSTAGEM+"</td>";
					tr += "<td value=>"+matriz[i].LOGIN+"</td>";
					tr += "<td>"+matriz[i].TEXTO+"</td>";
					tr += "<td>"+matriz[i].DATA_POSTAGEM+"</td>";
					
					tr += "<td>";
					
					if (<?php echo $_SESSION['login']['id']; ?> == matriz[i].ID_LOGIN){
						tr += "<button value='"+matriz[i].ID_POSTAGEM+"' class='remover'>Remover</button>";
						tr += "<button value='"+matriz[i].ID_POSTAGEM+"' class='alterar'>Alterar</button>";		
					}	
						
					tr += "<button value='"+matriz[i].ID_POSTAGEM+"' class='mostrar'>Mostrar Comentarios</button>";				
					tr += "</td></tr>";	
					tr += "<tr id='rc"+matriz[i].ID_POSTAGEM+"' style='display:none'><td colspan='6'>";
					tr += "<hr /><div id='c"+matriz[i].ID_POSTAGEM+"'></div>";

					
					
					tr += "<hr />";
					
					
					tr += "<div id='input"+matriz[i].ID_POSTAGEM+"'></div><button value='"+matriz[i].ID_POSTAGEM+"' class='comentar' style='display:block'>Comentar</button></td></tr>";
					
					
					carrega_comentario(matriz[i].ID_POSTAGEM, matriz[i].ID_LOGIN);
					$("tbody").append(tr);
				}
			}
		});
	}
	
	$(document).on("click", ".comentar", function(){
		var id_postagem = $(this).val();
		console.log(id_postagem);
		$.ajax({
			url: "insere_comentario.php",
			type: "post",
			data: {
				ID_POSTAGEM: id_postagem
			},
			success: function(d){
				$("#input"+id_postagem).html(d);
			}
		});
	});

	$(document).on("click", ".mostrar", function(){

		b = $(this);
	
		c = "#rc"+b.val();
		if(b.html() == "Mostrar Comentarios"){			
			$(c).show();
			$(".comentario").show();
			b.html("Esconder Comentarios");
			
		}else{
			$(c).hide();
			$(".comentario").hide();
			b.html("Mostrar Comentarios");
		}

		 
	 });


	function carrega_comentario(id_postagem, id_login){
		console.log("carrega_comentario");
		$.ajax({
			url: "comentario.php",
			type: "post",
			data: {
				FK: id_postagem,
				ID_LOGIN: id_login
			},
			success: function(d){
				$("#c"+id_postagem).html(d);
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
				$("input[name='ID_POSTAGEM']").val(dados.ID_POSTAGEM);
				$("input[name='TEXTO']").val(dados.TEXTO);
				$("input[name='DATA_POSTAGEM']").val(dados.DATA_POSTAGEM);
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
					ID_POSTAGEM: $("input[name='ID_POSTAGEM']").val(),
					TEXTO: $("input[name='TEXTO']").val(),
					DATA_POSTAGEM: $("input[name='DATA_POSTAGEM']").val(),
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
						$(".cadastrar").html("POSTAR");
						$("input[name='ID_POSTAGEM']").val("");
						$("input[name='TEXTO']").val("");
						$("input[name='DATA_POSTAGEM']").val("");
						$("input[name='ID_LOGIN']").val("");
						
						paginacao(pagina_atual);
					}
					else{
						console.log(d);
						$("#status").html("Postagem Não Alterada! Código já existe!");
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
					ID_POSTAGEM: $("input[name='ID_POSTAGEM']").val(),
					TEXTO: $("input[name='TEXTO']").val(),
					DATA_POSTAGEM: $("input[name='DATA_POSTAGEM']").val(),
					ID_LOGIN: $("input[name='ID_LOGIN']").val()
				 },
			beforeSend:function(){
				$("button").attr("disabled",true);
			},
			success: function(d){
				$("button").attr("disabled",false);
				if(d=='1'){
					$("#status").html("Postagem inserida com sucesso!");
					$("#status").css("color","green");
					carrega_botoes();
					paginacao(pagina_atual);
				}
				else{
					console.log(d);
					$("#status").html("Postagem Não Alterada! Código já existe!");
					$("#status").css("color","red");
				}
			}
		});
	});
	
	$(document).on("click",".cadastra_comentario",function(){
		$.ajax({
			url: "insere.php?tabela=COMENTARIO",
			type: "post",
			data: {
					ID_COMENTARIO: $("input[name='ID_COMENTARIO']").val(),
					TEXTO: $("input[name='TEXTO_COMENTARIO']").val(),
					DATA_COMENTARIO: $("input[name='DATA_COMENTARIO']").val(),
					ID_POSTAGEM: $("input[name='ID_POSTAGEM_COMENTARIO']").val(),
					ID_LOGIN: $("input[name='ID_LOGIN_COMENTARIO']").val()
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
					$("#status").html("Comentário Não inserido! Código já existe!");
					$("#status").css("color","red");
				}
			}
		});
	});
	
	$(document).on("click", ".remover_comentario", function(){
		id_remover = $(this).val();

		$.ajax({
			url: "remover.php",
			type: "post",
			data: {
				id: id_remover,
				tabela: "COMENTARIO"
			},
			success: function(d){
				console.log(d);
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
	
	$(document).on("click",".alterar_comentario",function(){
		id_alterar = $(this).val();	
		id_postagem = $("#id_post"+id_alterar).attr("value");		
		console.log(id_postagem);
		$.ajax({
			url: "get_dados_form.php",
			type: "post",
			data: {id: id_alterar, tabela: "COMENTARIO"},
			success: function(dados){
				$.ajax({
					url: "insere_comentario.php",
					type: "post",
					data: {
						ID_POSTAGEM: id_postagem
					},
					success: function(d){
						$("#input"+id_postagem).html(d);
						$("input[name='ID_COMENTARIO']").val(dados.ID_COMENTARIO);
						$("input[name='TEXTO_COMENTARIO']").val(dados.TEXTO);
						//$("input[name='ID_POSTAGEM_COMENTARIO']").val(dados.ID_POSTAGEM);
						$(".cadastra_comentario").attr("class","alterando_comentario");
						$(".alterando_comentario").html("ALTERAR");		
						console.log(dados);				
					}
				});
			}
		});
	});
	
		
		
	$(document).on("click",".alterando_comentario",function(){
		$.ajax({
			url:"altera.php?tabela=COMENTARIO",
			type: "post",
			data: {
				ID_COMENTARIO: $("input[name='ID_COMENTARIO']").val(),
				TEXTO: $("input[name='TEXTO_COMENTARIO']").val(),
				DATA_COMENTARIO: $("hidden[name='DATA_COMENTARIO']").val(),
				ID_POSTAGEM: $("input[name='ID_POSTAGEM_COMENTARIO']").val()
			 },
			beforeSend:function(){
				$("button").attr("disabled",true);
			},
			success: function(d){
				$("button").attr("disabled",false);
				if(d=='1'){
					$("#status").html("Comentário Alterado com sucesso!");
					$("#status").css("color","green");
					$(".alterando_comentario").attr("class","cadastra_comentario");
					$(".cadastra_comentario").html("CADASTRAR");
					$("input[name='ID_COMENTARIO']").val("");
					$("input[name='TEXTO']").val("");
					//$("hidden[name='DATA_COMENTARIO']").val("");
					
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
