<?php
namespace LCMS\Core{
	abstract class Based{
		private static $basedllwas=false;
		private static $base=null;
		abstract protected static function path();
		private static function load(){
			if(self::$base==null){
				self::$base=uncode(Path::get(static::path()), array());
			}
		}
		protected static function get($name, $def=null){
			self::load();
			if(isset(self::$base[$name])){
				return self::$base[$name];
			}else{
				return $def;
			}
		}
		protected static function set($name, $val){
			self::load();
			self::$basedllwas=true;
			self::$base[$name]=$val;
			return true;
		}
		protected static function delete($name){
			self::load();
			if(isset(self::$base[$name])){
				self::$basedllwas=true;
				unset(self::$base[$name]);
			}
			return true;
		}
		protected static function write(){
			if(self::$basedllwas){
				Path::put(static::path(), code(self::$base));
			}
		}
	}
}

