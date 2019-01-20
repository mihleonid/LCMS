<?php
namespace LCMS\Core{
	class Result{
		private $msg=null;
		public function __construct($message=""){
			$this->msg="";
			$this->append($message);
		}
		public function __toString(){
			return $this->get();
		}
		public function get(){
			return $this->msg;
		}
		public function add($msg){
			$this->append($msg);
		}
		public function append($msg){
			$this->msg.=$msg;
		}
	}
	class Action{
		private $path;
		public function __construct($action){
			if(strpos($action, ".")==false){
				$path=
			}
		}
		private
		public function e(){}
		public function i(){}
	}
}
?>