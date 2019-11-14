<?php
	//require_once("interfaceExibicao.php");

	class Option { //implements Exibicao{
		private $value;
		private $texto;
		private $selected;
		
		public function __construct($vetor,$selected){
			
			$this->selected = $selected;
			if(isset($vetor["value"])){
				$this->value=$vetor["value"];
			}
			$this->texto=$vetor["texto"];
		}
		
		public function exibe(){
			echo "<option";
			if($this->value!=null){
				echo " value='$this->value'";
			}
			if($this->value==$this->selected){
				echo " selected";
			}
			
			echo ">$this->texto</option>";
		}	
	}

?>