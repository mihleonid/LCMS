<?php
namespace LCMS\Core{
	class LAllowedTags extends IAllowedTags{
		public static function getAllowedTags($can=null){
			if($can==null){
				$can=User::can("html");
			}
			if($can){
				return Loc::get("html");
			}else{
				return Loc::get("tag");
			}
		}
		public static function addHTag($tag){
			$tag=strip($tag);
			if($tag=""){
				return new Result("---emptystr---");
			}
			$arr=Loc::get("html", array());
			$arr[$tag]=array();
			return Loc::set("html", $arr);
		}
		public static function addHAttr($tag, $attr){
			$arr=Loc::get("html");
			$tag=strip($tag);
			$attr=strip($attr);
			if(($tag="")or($attr="")){
				return new Result("---emptystr---");
			}
			if(!isset($arr[$tag])){
				$arr[$tag]=array($attr=>true);
			}
			$arr[$tag][$attr]=true;
			return Loc::set("html", $arr);
		}
		public static function deleteH($str){
			if(!is_array($str)){
				$str=array($str);
			}
			$r=new Result();
			foreach($str as $tag){
				$o=explode('/', (string)$tag);
				if(isset($o[1])){
					$r->add(static::deleteHAttr($o[0], $o[1]));
				}else{
					$r->add(static::deleteHTag($tag));
				}
			}
			return($r);
		}
		protected static function deleteHTag($tag){
			$arr=Loc::get("html");
			if(isset($arr[$tag])){
				unset($arr[$tag]);
				return Loc::set("html", $arr);
			}
			return new Result("---notag---");
		}
		protected static function deleteHAttr($tag, $attr){
			$arr=Loc::get("html");
			if(isset($arr[$tag][$attr])){
				unset($arr[$tag][$attr]);
				return Loc::set("html", $arr);
			}
			return new Result("---noattr---");
		}
		public static function addTag($tag){
			$tag=strip($tag);
			if($tag=""){
				return new Result("---emptystr---");
			}
			$arr=Loc::get("html", array());
			$arr[$tag]=array();
			return Loc::set("html", $arr);
		}
		public static function addAttr($tag, $attr){
			$arr=Loc::get("tag");
			$tag=strip($tag);
			$attr=strip($attr);
			if(($tag="")or($attr="")){
				return new Result("---emptystr---");
			}
			if(!isset($arr[$tag])){
				$arr[$tag]=array($attr=>true);
			}
			$arr[$tag][$attr]=true;
			return Loc::set("tag", $arr);
		}
		public static function delete($str){
			if(!is_array($str)){
				$str=array($str);
			}
			$r=new Result();
			foreach($str as $tag){
				$o=explode('/', (string)$tag);
				if(isset($o[1])){
					$r->add(static::deleteAttr($o[0], $o[1]));
				}else{
					$r->add(static::deleteTag($tag));
				}
			}
			return($r);
		}
		protected static function deleteTag($tag){
			$arr=Loc::get("tag");
			if(isset($arr[$tag])){
				unset($arr[$tag]);
				return Loc::set("tag", $arr);
			}
			return new Result("---notag---");
		}
		protected static function deleteAttr($tag, $attr){
			$arr=Loc::get("tag");
			if(isset($arr[$tag][$attr])){
				unset($arr[$tag][$attr]);
				return Loc::set("tag", $arr);
			}
			return new Result("---noattr---");
		}
	}
}
?>
