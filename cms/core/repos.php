<?php
namespace LCMS\Core{
	class Repos{
		public static function delete($name){
			return Path::delete(static::path($name));
		}
		public static function add($name, $io){
			return Path::put(static::path($name), IO::get($io));
		}
		public static function get($name){
			return uncode(Path::get(static::path($name)), "");
		}
		public static function exists($name){
			return(static::get($name)!="");
		}
		public static function path($name){
			return Path::cms(Path::concat("repos", strip($name).".cms"));
		}
	}
}

