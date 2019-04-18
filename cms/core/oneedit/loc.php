<?php
namespace LCMS\Core{
	class Loc extends Based{
		protected static function path(){
			return Path::cms("loc.db");
		}
		public static function set($name, $val){
			return parent::set($name, $val);
		}
		public static function get($name, $def=null){
			return parent::get($name, $def);
		}
		public static function delete($name){
			return parent::delete($name);
		}
		public static function shutdown(){
			static::write();
		}
	}
}
