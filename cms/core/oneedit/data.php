<?php
namespace LCMS\Core{
	class Data{
		protected static function path($module, $name="mane", $ext=null){
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
			return true;
		}
		public static function set($module, $name, $content){
			$path=static::path($module, $name, "dat");
			Path::put($path, code($content));
			return true;
		}
		public static function get($module, $name, $def=array()){
			$path=static::path($module, $name, "dat");
			return(uncode(Path::get($path), $def));
		}
	}
}
