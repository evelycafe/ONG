<script>
	
		$(document).ready(function(){
		 
		$("a").click(function(event){
		 var link = $(this);
		 
		if(link.attr("id").match("esconder"))
		 $("#MeuDiv").hide("slow");
		else
		 $("#MeuDiv").show("slow");
		 
		event.preventDefault();
		 
		 });
		 
		})
</script>

<?php
	require_once("conexao.php");
	
	
	
	require_once("classeControllerBD.php");
	

	$c = new ControllerBD($conexao);

	$FK = $_POST['FK'];
	echo $FK;
	$sql = "SELECT COMENTARIO.TEXTO 
FROM COMENTARIO INNER JOIN POSTAGEM ON COMENTARIO.ID_POSTAGEM= $FK";
	
	$stmt = $conexao->prepare($sql);
			
	$stmt->execute();
	$linha = $stmt->fetch(PDO::FETCH_ASSOC);
		

	echo $linha["TEXTO"];
	//foreach($linha as $i=>$v){
	
	$divs = "";
	for ($i = 0; $i < sizeof($linha); $i++){
		$divs .= "<div id='MeuDiv' style='background-color:orange;'>
	 			<p>".$linha["TEXTO"]."</p>
	 		</div>";
	 	echo $divs;
	 	
	}
	 $divs .= "<a id='esconder' href=''>Esconder</a>
	 <a id='exibir' href=''>Mostrar</a>";
	 
	 echo $divs;
	 json_encode($divs);


	
	
?>
