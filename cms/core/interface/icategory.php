<?php
namespace LCMS\Core{
	abstract public class ICategory extends GlobalRW{
		protected static function path();
		abstract public static function set($name, $ops);
		abstract public static function exists($name);
		abstract public static function realName($name);
	}
}
?>