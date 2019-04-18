<?php
namespace LCMS\Core{
	class AntiXSS{
		public static function U(){
			$u=@header("Access-Control-Allow-Origin: http://www.nt10.ru", true);
			@header("Allow-Origin: http://www.nt10.ru", true);
			@header("Access-Control-Allow-Credentials: false", true);
			@header("Vary: Origin", true);
			return $u;
		}
		public static function S(){
			$u=AntiXSS::U();
			@header("X-Frame-Options: sameorigin;", true);
			@header("X-Frame-Options: Sameorigin;", false);
			@header("x-xss-protection: 1; mode=block", true);
			@header("x-frame-options: SAMEORIGIN", true);
			@header("x-frame-options: sameorigin", false);
			return $u;
		}
		public static function H(){
			$a=AntiXSS::U();
			@header("X-Frame-Options: deny;", true);
			@header("X-Frame-Options: Deny;", false);
			@header("x-xss-protection: 1; mode=block", true);
			@header("x-frame-options: DENY", true);
			@header("x-frame-options: deny", false);
			return $a;
		}
		public static function R(){
			if(!isset($_SERVER['HTTP_REFERER'])){
				AntiXSS::hacker();
			}
			if(!isset($_SERVER['HTTP_HOST'])){
				AntiXSS::hacker();
			}
			if(!preg_match("@https?://".$_SERVER['HTTP_HOST']."/cms/.*@", $_SERVER['HTTP_REFERER'])){
				AntiXSS::hacker();
			}
		}
		public static function getA(){
			return Loc::get("antixsslog", true);
		}
		public static function setA($b){
			return Loc::set("antixsslog", tobool($b));
		}
		public static function resetA(){
			static::setA(!static::getA());
		}
		public static function hacker(){
			if(static::getA()){
				ELog::logged("[HACKER ATTACK] from: ".((isset($_SERVER['HTTP_REFERER']))?(html($_SERVER['HTTP_REFERER'])):("UNKNOWN")).", to:".((isset($_SERVER['PHP_SELF']))?($_SERVER['PHP_SELF']):("UNKNOWN")).", in:".date("H:i:s").'&nbsp;'.date("d.m.Y"));
				die("HACKER ATTACK! YOU ARE LOGGED AND SEEKED!");
			}else{
				exit;
			}
		}
	}
}

