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
	abstract class IAllowedTags{
		abstract public function getAllowedTags($can=null);
		abstract public function addHTag($tag);
		abstract public function addHAttr($tag, $attr);
		abstract public function deleteH($path);
		abstract public function addTag($tag);
		abstract public function addAttr($tag, $attr);
		abstract public function delete($path);
	}
	class TLogClear{
		public static function clear($n){
			$log=Path::get(static::path());
			$log=explode("\n", $log);
			$n=min($n, count($log));
			$n=count($log)-$int;
			for($i=0;$i<$n;++$i){
				unset($log[$i]);
			}
			Path::put(static::path(), implode("\n", $log));
			return new Result();
		}
	}
	abstract class ILog extends TLogClear{
		public static function path(){
			return Path::cms("main.log");
		}
		public static function put($msg){
			return static::llog(static::path(), $msg);
		}
		abstract public static function logging();
		abstract public static function llog($path, $msg);
	}
	abstract class IPageLog extends TLogClear{
		const ADD=2;
		const EDIT=3;
		const DELETE=4;
		public static function path(){
			return Path::cms("page.log");
		}
		abstract public function put($path, $user, $type, $ok=true);
		public static function logging(){
			return true;
		}
	}
	abstract class IPageList{
		public static function path(){
			return(Path::cms("pages.db"));
		}
		abstract public function add($path, $name, $category);
		abstract public function delete($path);
		abstract public function all();
		public function cleanup(){
			$all=static::all();
			foreach($all as $path=>$val){
				if(trim(Path::get($path))==""){
					static::delete($path);
				}
			}
		}
	}
	abstract public class User{
		abstract public function exists($name);
		abstract public function can($name, $stat);
		abstract public function realName($name);
		abstract public function authName();
	}
	abstract public class Status{
		
	}
	abstract public class Permission{
		
	}
}
?>