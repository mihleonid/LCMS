<?php
namespace LCMS\Core{
	class Localization{
		private static $conf=null;
		private static function initialize(){
			if($conf==null){
				$conf=new ConfigSK("locale");
			}
		}
		public static function setloc($name){
			return Loc::set("currentlocale", $name);
		}
		public static function getloc(){
			return Loc::get("currentlocale", "en");
		}
		public static function get($name){
			static::initialize();
			$cur=static::getloc();
			$res=$conf->get($cur, $name);
			if($res==null){
				$res=$conf->get("en", $name);
				if($res==null){
					return $name;
				}
			}
			return $res;
		}
		public static function set($name, $val, $lang=null){
			static::initialize();
			if($lang==null){
				$lang=static::getloc();
			}
			$lang=strip($lang);
			if($lang==null){
				$lang=static::getloc();
			}
			$conf->set($lang, $name, $val);
		}
		public static function setall($text){
			static::initialize();
			$conf->setall($text);
		}
		public static function cleanup(){
			static::initialize();
			$conf->cleanup();
		}
		public static function update(){
			$conf->update
			static::initialize();
		}
	}
}
