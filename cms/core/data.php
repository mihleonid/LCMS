<?php
namespace LCMS\Core{
	class Data{
		public static function path($module, $name="mane", $ext=null){
			if($name==null){
				$name="mane";
			}else{
				
				$name=strip($name);
			}
			if($module==null){
				$module="storage";
			}else{
				$module=strip($module);
			}
			if($module=="storage"){
				return Path::cms("storage/".$name.$ext);
			}else{
				if(Moduler::exists($module)){
					return Path::concat(Moduler::path($module), "data/".$name.$ext);
				}else{
					return Path::cms("storage/".$name.$ext);
				}
			}
		}
		public static function delete($module, $name){
			$path=static::path($module, $name, "dat");
			Path::delete($path);
			return new Result();
		}
		public static function set($module, $name, $content){
			$path=static::path($module, $name, "dat");
			Path::put($path, code($content));
			return new Result();
		}
		public static function get($module, $name, $def=array()){
			$path=static::path($module, $name, "dat");
			return(uncode(Path::get($path), $def));
		}
	}
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