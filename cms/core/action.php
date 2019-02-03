<?php
namespace LCMS\Core{
	class Action{
		private $content;
		private static function path($act){
			$path=str_ireplace(".php", "", $act);
			if(strpos($action, ".")==false){
				$path=Path::cms(Path::concat("actions", $act.".php"));
			}else{
				$path=explode(".", $path, 2);
				if(Moduler::exists($path[0])){
					$path=Path::cms(Path::concat("actions", $path[0], $path[1].".php"));
				}else{
					$path=Path::tmpfile("act");
				}
			}
			$this->parse($params);
		}
		public static function exists($act){
			return(Path::get(static::path($act))!="");
		}
		public function __construct($action,  $params=array(), $path=true){
			if($path){
				$this->content=Path::get(static::path($action));
				$this->a=strip($action, true, '\\.');
			}else{
				$this->content=$action;
				$this->a="MOMENT";
			}
			$this->parse($params);
		}
		private $action;
		private $form;
		private $can;
		private $header;
		private $a;
		private function parse($params){
			$this->content=Text::parse($this->content, Text::PARSE_LOCALE|Text::PARSE_PLUGIN|Text::PARSE_SPC_ACT, array('a'=>$this->a));
			if(is_null($params)){
				$params=array();
			}elseif(is_string($params)or is_int($params)or is_bool($params)){
				$params=array('all'=>$params);
			}
			if(!is_array($params)){
				throw new \Exception("Incorrect params");
			}
			foreach($params as $k=>$l){
				if($l){
					if(strpos($this->content, "***")!==false){
						$j=preg_quote($k, "@");
						$this->content=preg_replace("@\*\*$j\*\*(.*?)\*\*\*@s", "\\1", $this->content);
						$this->content=preg_replace("@\*\*!$j\*\*(.*?)\*\*\*@s", "", $this->content);
					}
				}else{
					if(strpos($this->content, "***")!==false){
						$j=preg_quote($k, "@");
						$this->content=preg_replace("@\*\*!$j\*\*(.*?)\*\*\*@s", "\\1", $this->content);
						$this->content=preg_replace("@\*\*$j\*\*(.*?)\*\*\*@s", "", $this->content);
					}
				}
				$PARAM='$PARAM$'.$k.'$';
				if(strpos($this->content, $PARAM)){
					$this->content=str_replace($PARAM, $l, $this->content);
				}
				$PARAM='&PARAM&'.$k.'&';
				if(strpos($this->content, $PARAM)){
					$this->content=str_replace($PARAM, "unserialize('".str_replace(array('\\', '\''), array('\\\\', '\\\''), serialize($l))."')", $this->content);
				}
			}
			$this->content=preg_replace("@\*\*!(.*?)\*\*(.*?)\*\*\*@s", "\\2", $this->content);
			$this->content=preg_replace("@\*\*(.*?)\*\*(.*?)\*\*\*@s", "", $this->content);
			if(User::isClever()){
				$this->content=preg_replace('@\*CLEVER\*(.*?)\*\*\*@s', "\\1", $this->content);
			}
			$xml=new XML($this->content);
			if($xml->has("CLEVER")){
				if(!User::isClever()){
					return null;
				}
			}
			$pre=$xml->node("PRE");
			if(!User::can($pre)){
				$this->can=false;
				return null;
			}
			$classes=explode(",", $xml->node("CLASS"));
			foreach($classes as $cl){
				if(!class_exists($cl)){
					$this->can=false;
					return new Result("---noclass--- [".html($a)."][".html($cl)."]");
				}
			}
			$this->header=$xml->node("HEADER");
			$this->form=$xml->node("FORM");
			$this->action=$xml->node("ACTION");
			if(($this->form=="")and($this->action=="")){
				$this->can=false;
			}else{
				$this->can=true;
			}
		}
		public function get(){
			if($this->can){
				ob_start();
				eval("?".">".($this->form)."<"."?php");
				$c=ob_get_contents();
				ob_end_clean();
				return((($this->h)?("<h3>".trim($this->header)."</h3>"):("")).trim($c));
			}
			return "";
		}
		public function e(){
			echo($this->get);
		}
		public function i(){
			if($this->can){
				ob_start();
				$res=eval($us.str_replace("?>", " ", str_replace("<?php", " ", $this->act)));
				ob_end_clean();
				return $res;
			}else{
				return new Result("---noprm---");
			}
		}
		public function ee($action,  $params=array(), $path=true){
			$a=new Action($action,  $params=array(), $path=true);
			$a->e();
		}
		public function ii($action,  $params=array(), $path=true){
			$a=new Action($action,  $params=array(), $path=true);
			return $a->i();
		}
	}
}

