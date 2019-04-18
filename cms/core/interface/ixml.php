<?php
namespace LCMS\Core{
	abstract class IXML{
		protected $contents="";
		public function __construct($path, $path=true){
			if($path){
				$this->contents=IO::get($path);
			}else{
				$this->contents=$path;
			}
		}
		public function code(){
			return code($this->contents);
		}
		public function uncode($a){
			$this->contents=uncode($a, "");
		}
		public function zip(){
			return $this;
		}
		public function __toString(){
			return $this->contents;
		}
		public function deleteComments(){
			$this->contents=str_replace("<!---->", "", $this->contents);
			$this->contents=preg_replace("@<!--[^!].*?-->@", "", $this->contents);
			return $this;
		}
		abstract public function has($tag, $args=array());
		abstract public function hasopen($tag, $args=array());
		abstract public function node($path);
		abstract public function getNodes($path);
	}
}
