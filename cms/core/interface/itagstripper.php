<?php
namespace LCMS\Core{
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
		public static function s($text, $tags=array(), $php=true){
			$r=new static($php, $tags);
			return($r->strip($text));
		}
		abstract public function strip($text);
	}
}
?>