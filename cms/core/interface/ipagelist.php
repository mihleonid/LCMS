<?php
namespace LCMS\Core{
	abstract class IPageList extends GlobalRW{
		protected static function path(){
			return(Path::cms("pages.db"));
		}
		abstract public static function add($path, $name, $category);
		abstract public static function delete($path);
		abstract public static function all();
		public function cleanup(){
			$all=static::all();
			foreach($all as $path=>$val){
				if(trim(Path::get($path))==""){
					static::delete($path);
				}
			}
		}
	}
}
