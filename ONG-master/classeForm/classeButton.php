<?php
	
	class Button {//implements Exibicao{
		private $texto;
		private $type;
		private $id;
		private $class;
		
		public function __construct($vetor){
			$this->texto = $vetor["texto"];
			if(isset($vetor["type"])){
				$this->type=$vetor["type"];
			}
			if(isset($vetor["id"])){
				$this->id = $vetor["id"];
			}
			if(isset($vetor["class"])){
				$this->class = $vetor["class"];
			}			
		}
		
		public function exibe(){
			echo "<button";
			if($this->type!=null){
				echo " type='$this->type'";
			}
			if($this->id!=null){
				echo " id='$this->id'";
			}
			if($this->class!=null){
				echo " class='$this->class'";
			}
			echo ">$this->texto</button>";
		}
		
	}
?>