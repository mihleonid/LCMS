<?php
namespace LCMS\Core{
	class Config{
		private $cnt="";
		private $parsed=null;
		private $p=null;
		const PATH=1;
		public function __construct($con, $path=true){
			if($path){
				$this->p=$con;
				$this->cnt=IO::get($con);
			}else{
				$this->cnt=trim((string)$con);
			}
		}
		private function parse(){
			if($this->parsed==null){
				$this->uparse();
			}
			return $this->parsed;
		}
		private function uparse(){
			$this->parsed=array();
			$c=explode("\n", $this->cnt);
			foreach($c as $v){
				$v=Code::deleteCommentLine($v, "#");
				$v=Code::deleteCommentLine($v);
				$v=trim($v);
				$v=explode("=", $v);
				if(!isset($v[1])){
					$v[1]="1";
				}
				$v[0]=strip($v[0], false, "\\\\");
				$this->parsed[$v[0]]=trim($v[1]);
			}
		}
		public function get($key, $def=""){
			static::parse();
			if(!isset($this->parsed[$key])){
				return $def;
			}
			return $this->parsed[$key];
		}
		public function set($key, $val){
			$this->cnt.="\n".strip($key, false, "\\\\")."=".str_replace("\r", "", str_replace($val, "\n", ""));
			static::uparse();
			return $this;
		}
		public function write($path=self::PATH){
			if($path==static::PATH){
				if($this->p!=null){
					return IO::set($this->p, trim($this->cnt)."\n");
				}else{
					return false;
				}
			}else{
				return IO::set($path, trim($this->cnt)."\n");
			}
		}
	}
}
?>
