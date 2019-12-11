<html>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js" type="text/javascript"></script>
	<script src="toggle.js" type="text/javascript"></script>
	
	<head>
		<title>Esconder e Exibir Divs com Jquery</title>
		
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
	</head>
	<body>
		 <div id="MeuDiv" style="background-color:orange; width:500px; height:500px;">
		 Hello Word
		 </div>
		 <a id="esconder" href="http://jquery.com/">Esconder [JQuery]</a>
		 <a id="exibir" href="http://google.com/">Mostrar [Google]</a>
		 
		 
	
	</body>
</html>
