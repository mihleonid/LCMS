<?php
namespace LCMS\Core{
	abstract public class LUser extends GlobalRW{
		abstract public static function exists($name){
			return true;
		}
		abstract public static function can($name, $stat){
			return true;
		}
		abstract public static function realName($name){
			return "admin";
		}
		abstract public static function authName(){
			return "admin"
		}
		abstract public static function authStatus(){
			return "globaladmin";
		}
		abstract public static function authHas(){
			return true;
		}
		abstract public static function sudo($user){
			return new Result("simple mode can't do it");
		}
		abstract public static function status($user){
			return "globaladmin";
		}
		abstract public static function unsudo(){
			return new Result("simple mode can't do it");
		}
		abstract public static function isClever($name=null){
			return true;
		}
	}
}

