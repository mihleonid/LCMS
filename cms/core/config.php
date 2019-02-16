<?php
namespace LCMS\Core{
	class Config{
		private $cnt="";
		private $parsed=null;
		private $p=null;
		private $cmnt="#";
		const PATH=1;
		public function __construct($con, $path=true, $c="#"){
			$this->setComment($c);
			if($path){
				$this->p=$con;
				$this->cnt=IO::get($con);
			}else{
				$this->cnt=trim((string)$con);
			}
		}
		public function setComment($c){
			$l=$this->cmnt;
			$this->cmnt=$c;
			if($l!=$c){
				$this->uparse();
			}
		}
		public function getComment(){
			return $this->cmnt;
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
				$v=Code::deleteCommentLine($v, $this->cmnt);
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
		/*smrtcleanup
		public function cleanup(){
			$cnt="";
			$lines=array();
			$c=explode("\n", $this->cnt);
			foreach($c as $v){
				$v=trim($v);
				$v=trim($v, "#");
				$v=Code::commentLine($v, "#");
				if(trim($v[0])==""){
					$lines[]=$v[1];
				}
				$c=explode("=", $v[0]);
				if(!isset($c[1])){
					$c[1]="1";
				}
			}
		}
		*/
		public function cleanup(){
			$newfile=array();
			$arr=explode("\n", $this->cnt);
			foreach($arr as $line){
				$line=trim($line);
				if(strpos($line, $this->cmnt)!==false){
					$newfile[]=$line;
					continue;
				}
				if(strpos($line, "=")==false){
					continue;
				}
				if(!in_array($line, $newfile)){
					$newfile[]=$line;
				}
			}
			$this->cnt=implode("\n", $newfile);
			$this->uparse();
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
