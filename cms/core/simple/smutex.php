<?php
namespace LCMS\Core{
	class LMutex extends IMutex{
		public static function set($module, $name){
			$path=Path::tmp($module."ll".$name);
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
				Log::put("Out of mutex");
			}
			Path::set($path, "muted");
		}
		public static function delete($module, $name){return(Path::delete(Path::tmp($module."ll".$name)));}
	}
}
