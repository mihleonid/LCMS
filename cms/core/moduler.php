<?php
namespace LCMS\Core{
	class Moduler{
		const SIMPLE_LEVEL=0;
		const STANDART_LEVEL=1;
		const USER_LEVEL=2;
		private static $base=null;
		private static function load(){
			if(static::$base==null){
				static::$base=uncode(Path::get(Path::cms("modules.db")), array());//format name=>array(group, level);
			}
		}
		private static function leveltostring($level){
			if($level==static::USER_LEVEL){
				return("usermodules");
			}else{
				if($level==static::STANDART_LEVEL){
					return("stdmodules");
				}else{
					return("simplemodules");
				}
			}
		}
		public static function getBase(){
			return(static::$base);
		}
		public static function exists($name){
			static::load();
			return(isset(static::$base[$name]));
		}
		public static function path($name){
			if(static::exists($name)){
				$line=static::$base[$name];
				$level=static::leveltostring($line[1]);
				return(Path::cms($level."/".$line[0]."name"));
			}else{
				if(Log::logging()){
					Log::put("Module ".strip($name)." not exists");
				}
				return(Path::tmp("rubish".rand()));
			}
		}
		public static function log($module, $message){
			if(Log::logging()){
				Log::llog(Path::concat(static::path($module), "main.log"), $message);
			}
		}
		public static function install($module){
			if(!Repos::exists($module)){
				return new Result("---nomodule---");//loc
			}
		}
	}
}
?>
