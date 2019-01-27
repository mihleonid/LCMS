<?php
namespace LCMS\Core{
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
}
?>