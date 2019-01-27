<?php
namespace LCMS\Core{
	class Category{
		public static function set($name, $ops){
			$categories=Loc::get("category");
			if(isset($categories[$name])){
				$categories[$name]=$ops;
				return Loc::set("category", $categories);
			}else{
				return new Result("Категории не существует");
			}
		}
		public static function delete($name){
			$categories=Loc::get("category");
			if(isset($categories[$name])){
				unset($categories[$name]);
				return Loc::set("category", $categories);
			}else{
				return new Result("Категории не существует");
			}
		}
		public static function exists($name){
			$categories=Loc::get("category");
			return(isset($categories[$name]));
		}
	}
}
?>