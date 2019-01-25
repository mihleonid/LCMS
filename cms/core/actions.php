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
		public function put($msg){
			$this->append($msg);
		}
		public function set($msg){
			$this->append($msg);
		}
		public function append($msg){
			if(is_class_of($msg, "\\LCMS\\Core\\Result")){
				$this->msg.=$msg->get();
			}else{
				$this->msg.=$msg;
			}
		}
		public function g(){
			return(Text::parse($this->msg, Text::PARSE_LOCALE));
		}
		public function e(){
			echo($this->g());
		}
	}
	class Action{
		private $path;
		public function __construct($action){
			if(strpos($action, ".")==false){
				$path=
			}
		}
		public function e(){}
		public function i(){}
	}
}
?>