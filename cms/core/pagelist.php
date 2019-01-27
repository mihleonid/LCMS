<?php
namespace LCMS\Core{
	class PageList extends IPageList{
		public static function add($path, $name, $category){
			$path=Path::iabs($path);
			if(trim(Path::get($path))!=""){
				$c=static::all();
				$name=strip_ru($name);
				$category=Category::realName($category);
				$all=static::all();
				$all[$path]=array($name, $category);
				return static::write($all);
			}
		}
		public static function delete($path){
			$path=Path::iabs($path);
			$all=static::all();
			if(isset($all[$path])){
				unset($all[$path]);
			}
			return static::write($all);
		}
	}
}
?>