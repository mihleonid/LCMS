<?php
namespace LCMS\Core{
	abstract class GlobalRW{
		abstract protected static function path();
		protected static function all(){
			return uncode(Path::get(static::path()), array());
		}
		protected static function write($c){
			return Path::put(static::path(), code($c));
		}
	}
}
