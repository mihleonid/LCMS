<?php
namespace LCMS\Core{
	abstract class Based{
		private static $basedllwas=false;
		private static $base=null;
		abstract protected static function path();
		private static function load(){
			if(static::$base==null){
				static::$base=uncode(Path::get(static::path()), array());
			}
		}
		protected static function get($name, $def=null){
			static::load();
			if(isset(static::$base[$name])){
				return static::$base[$name];
			}else{
				return $def;
			}
		}
		protected static function set($name, $val){
			static::load();
			static::$basedllwas=true;
			static::$base[$name]=$val;
			return true;
		}
		protected static function delete($name){
			static::load();
			if(isset(static::$base[$name])){
				static::$basedllwas=true;
				unset(static::$base[$name]);
			}
			return true;
		}
		protected static function write(){
			if(static::$basedllwas){
				Path::put(static::path(), code(static::$base));
			}
		}
	}
}
