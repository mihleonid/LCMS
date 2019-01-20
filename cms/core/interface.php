<?php
namespace LCMS\Core{
	abstract class ILXML{
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
	abstract class ITagStripper{
		protected $allowed=array();//format [tag]=>array([attr]=>true)
		protected $PHP=true;
		public function __construct($stripphp=true, $tags=array()){
			$this->setAllowedTags($tags);
			$this->setPHPMode($stripphp);
		}
		public function setPHPStripping($php){
			if($php){
				$this->PHP=true;
			}else{
				$this->PHP=false;
			}
		}
		public function setAllowedTags($tags){
			if(!is_array($tags)){
				$tags=array();
			}
			foreach($tags as $t=>$a){
				if(!is_string($t)){
					unset($tags[$t]);
				}else{
					if(!is_array($a)){
						$tags[$t]=array();
					}
				}
			}
			$this->allowed=$tags;
		}
	}
}
?>