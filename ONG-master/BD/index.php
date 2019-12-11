<?php
	require_once ("../classeLayout/classeCabecalhoHTML.php");
	require_once("cabecalho.php");
?>

<script>
	function trocaImgAna(img){
		//var img = document.getElementById("teste");
		img.src="../imagens/anaChange.jpg";
	}
	function trocaImgEv(img){
		//var img = document.getElementById("teste");
		img.src="../imagens/evelynChange.jpg";
	}
	function ImgAna(img){
		//var img = document.getElementById("teste");
		img.src="../imagens/ana2.jpg";
	}
	function ImgEv(img){
		//var img = document.getElementById("teste");
		img.src="../imagens/evely2.jpg";
	}
	
</script>

	<section class="courses">
		<hgroup>
			<h2>AAEC</h2>
			<h3>Sejam Bem Vindos</h3>
		</hgroup>
		<p><center> &#160 &#160 Adotar é um ato de amor. E dedicar-se a outro ser vivo, dando-lhe afeto, cuidados e atenção, é parte disso. É uma alegria ver como cães e gatos têm conquistado um lar acolhedor, que os protege dos maus tratos das ruas.</center></p>
		<p><center><b>(Newt Scamander)</b></center></p>
		<br/>
		<p><center> &#160 &#160 É por amor que criamos este projeto. Com o objetivo de ajudar estes pequenos anjos a encontrarem suas famílias. A encontrar lares e pessoas que estejam dispostas a retribuir o amor que só eles podem oferecer. Um amor puro sem outras intenções.</center></p>
		<p><center> &#160 &#160 Se você está disposto a ama-los, assim como nós os amamos, então não perca tempo. Faça parte desta ONG, e junte-se a nossa grande família.</center></p>
	</section>

	<aside>
		<section>
			<h2>Criadoras da AAEC</h2>
			<center><img id="ana" src="../imagens/ana2.jpg" onMouseOver="trocaImgAna(this);" onMouseOut="ImgAna(this)" ></center>
			<center><img id="evelyn" src="../imagens/evely2.jpg" onMouseOver="trocaImgEv(this);" onMouseOut="ImgEv(this)"></center>
			<center><button><a href="saibamais.php" target="_blank" >Saiba Mais</a></button></center>
		</section>
	</aside>

	<footer>
		&copy; 2019, AAEC<br/>
		Adotamos um ao outro,  simples  assim: porque eu precisava dele e ele de mim. (Alessandra Grani)
	</footer>

</div>
</body>
</html>
