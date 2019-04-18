<?php
namespace LCMS\Core{
	class ConfigSK{
		private static $base=null;
		private static $name=null;
		public function __construct($name){
			$this->name=strip($name);
		}
		private function cachepath(){
			return Path::cms("cache/".$this->name.".tmp");
		}
		private function path(){
			return Path::cms("config/".$this->name.".txt");
		}
		public function get($sub, $name){
			$this->uread();
			if(!isset($this->base[$sub])){
				return null;
			}
			if(!isset($this->base[$cur][$name])){
				return null;
			}
			return $this->base[$cur][$name];
		}
		public function set($sub, $name, $val){
			$name=strip($name);
			$val=str_replace("\r\n", " ", $val);
			$val=str_replace("\n", " ", $val);
			$val=str_replace("\r", " ", $val);
			$val=str_replace("\t", " ", $val);
			$lang=strip($lang);
			Path::append($this->path(), $name."[".$lang."]: ".$val);
			$this->update();
		}
		public function setall($text){
			Path:put($this->path(), $text);
			$this->update();
		}
		private static function uread(){
			if($this->base==null){
				$this->read();
			}
		}
		private static function read(){
			$this->base=uncode(Path::get($this->cachepath()), null);
			if($this->base==null){
				$this->base=array();
				$tmp=Path::getlines($this->path());
				foreach($tmp as $line){
					$line=Code::deleteCommentLine($line);
					$ttmp=explode(":", $line);
					if(!isset($ttmp[1])){
						continue;
					}
					$ttmp[0]=trim($ttmp[0]);
					$ttmp[1]=trim($ttmp[1]);
					$ttmp[0]=substr($ttmp[0], 0, strlen($ttmp[0])-1);
					$ttt=explode("[", $ttmp[0]);
					if(!isset($ttt[1])){
						continue;
					}
					$ttt[0]=trim($ttt[0]);
					$ttt[1]=trim($ttt[1]);
					if(!isset($this->base[$ttt[1]])){
						$this->base[$ttt[1]]=array();
					}
					$this->base[$ttt[1]][$ttt[0]]=$ttmp[1];
				}
				Path::put($this->cachepath(), code($this->base));
			}
		}
		public static function cleanup(){
			Path::delete($this->cachepath());
			$newfile=array();
			$arr=Path::getlines($this->path());
			foreach($arr as $line){
				$line=trim($line);
				if(strpos($line, "//")!==false){
					$newfile[]=$line;
					continue;
				}
				if(strpos($line, "[")==false){
					continue;
				}
				if(strpos($line, "]")==false){
					continue;
				}
				if(strpos($line, ":")==false){
					continue;
				}
				if(!in_array($line, $newfile)){
					$newfile[]=$line;
				}
			}
			$this->read();
		}
		public static function update(){
			Path::delete($this->cachepath());
			$this->read();
		}
	}
}
