<?php
namespace LCMS\Core{
	class Handler{
		private static $base=array();//format group=>array(...func=>module...)
		private static function path($g){
			return(Path::cms("handlers/".strip($g).".db"));
		}
		private static function uload(){
			static::$base[$g]=uncode(Path::get(static::path($g)));
		}
		private static function load($g){
			if(!isset(static::$base[$g])){
				static::uload();
			}
		}
		private static function flush($g){
			Path::put(static::path($g), code(static::$base[$g]));
		}
		public static function invoke($g, $data=array()){
			$g=strip($g);
			Pool::setCwd();
			static::load($g);
			foreach(static::$base[$g] as $func=>$module){
				if(function_exists($func)){
					try{
						@($func($data));
					}catch(\Exception $e){
						Moduler::log($module, "Crash (exc) in invokation function ".$func." with message".($e->getMessage()));
					}catch(\Throwable $e){
						Moduler::log($module, "Crash (exc) in invokation function ".$func." with message".($e->getMessage()));
					}
				}
			}
		}
		public static function initialize(){
			static::invoke("initialize");
		}
		public static function cleanupSelf(){
			$files=Path::scan(Path::cms("handlers"));
			foreach($files as $gdb){
				$ar=uncode(Path::get(Path::cms("handlers/".$gdb)), array());
				foreach($ar as $func=>$module){
					if($module!=-1){
						if(!Moduler::exists($module)){
							unset($ar[$func]);
							continue;
						}
					}
					if(!function_exists($func)){
						unset($ar[$func]);
						if(Log::logging()){
							Moduler::log($module, "Deleted function ".$func." from handling ".substr($gdb, 0, strlen($gdb)-3));
						}
					}
				}
				if(count($ar)==0){
					Path::delete(Path::cms("handlers/".$gdb));
				}else{
					Path::put(Path::cms("handlers/".$gdb), code($ar));
				}
			}
		}
		public static function cleanup(){
			static::invoke("cleanup");
		}
		public static function shutdown(){
			static::invoke("shutdown");
		}
		public static function exc($data=array()){//exception
			static::invoke("exc", $data);
		}
		public static function err($data=array()){
			static::invoke("err", $data);
		}
		public static function set($g, $func, $module=-1){
			if($module!=-1){
				if(!Moduler::exists($module)){
					$module=-1;
				}
			}
			if($module==0){
				$module=-1;
			}
			$g=strip($g);
			static::load($g);
			if(function_exists($func)){
				if(!isset(static::$base[$g])){
					static::$base[$g]=array();
				}
				static::$base[$g][$func]=$module;
				static::flush($g);
			}else{
				if($module!=-1){
					if(Log::logging()){
						Moduler::log($module, "Cannot set handler to ".$g." with non existing function (stripped)".strip($func));
					}
				}
			}
		}
		public static function delete($g, $func){
			$g=strip($g);
			static::load($g);
			if(isset(static::$base[$g])){
				if(isset(static::$base[$g][$func])){
					unset(static::$base[$g][$func]);
					static::flush();
				}
			}
		}
	}
}
?>