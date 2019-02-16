<?php
namespace LCMS\Core{
	abstract class LUser extends GlobalRW{
		public static function exists($name){
			return true;
		}
		public static function can($name, $stat=null){
			return true;
		}
		public static function realName($name){
			return "admin";
		}
		public static function authName(){
			return "admin";
		}
		public static function authStatus(){
			return "globaladmin";
		}
		public static function authHas(){
			return true;
		}
		public static function sudo($user){
			return new Result("simple mode can't do it");
		}
		public static function status($user){
			return "globaladmin";
		}
		public static function unsudo(){
			return new Result("simple mode can't do it");
		}
		public static function isClever($name=null){
			return true;
		}
	}
}

