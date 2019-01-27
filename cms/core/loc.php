<?php
namespace LCMS\Core{
	class Loc{
		private static $base=null;
		private static $was=false;
		private static function load(){
			if(static::$base==null){
				static::$base=uncode(Path::get(Path::cms("loc.db")), array());
			}
		}
		public static function get($name, $def=null){
			static::load();
			if(isset(static::$base[$name])){
				return static::$base[$name];
			}else{
				return $def;
			}
		}
		public static function set($name, $val){
			static::load();
			static::$was=true;
			static::$base[$name]=$val;
			return new Result();
		}
		public static function delete($name){
			static::load();
			if(isset(static::$base[$name])){
				static::$was=true;
				unset(static::$base[$name]);
			}
			return new Result();
		}
		public static function shutdown(){
			if(static::$was){
				Path::put(Path::cms("loc.db"), code(static::$base));
			}
		}
	}
}
?>