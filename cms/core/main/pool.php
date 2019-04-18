<?php
namespace LCMS\Core{
	final class Pool{
		const CRASH="LCMS_CRASH%COMPIELER:RANDOM%";
		private static $cwd=null;
		private static $flushback=false;
		public static function getCwd(){
			static::$cwd=getcwd();
		}
		public static function setCwd(){
			if((static::$cwd!=null)and(static::$cwd!=false)){
				chdir(static::$cwd);
			}
		}
		public static function initialize(){
			static::getCwd();
		}
		public static function getFlushBack(){
			return static::$flushback;
		}
		public static function setFlushBack($b){
			static::$flushback=tobool($b);
		}
		public static function trouble(){
			static::setFlushBack(true);
		}
		public static function caught(){
			static::setFlushBack(false);
		}
	}
}
