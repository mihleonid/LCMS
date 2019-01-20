<?php
namespace LCMS\Core{
	use \LCMS\MM\Logger\Log;
	class Mutex{
		public static function set($module, $name){
			$path=Data::path($module, $name, "mut");
			$tries=Loc::get("mutabletries", "10");#todo control
			$muted=false;
			while(Path::get($path)=="muted"){
				$muted=true;
				$tries--;
				if($tries<0){
					break;
				}
				sleep(1);
			}
			if($muted){
				if(!tobool(Loc::get("agressive", false))){#todo control
					throw new SafetyException("Out of mutex");
				}
				Log::put("Out of mutex");
			}
		}
		public static function delete($module, $name){return(Path::delete(Data::path($module, $name, "mut")));}
	}
	class SafetyException extends \Exception{
		public function __construct($message, $code=0, $previous=null){
			Pool::trouble();
			parent::__construct($message, $code, $previous);
		}
	}
}
?>