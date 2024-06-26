<?php
namespace LCMS\Core{
	abstract class IUser extends GlobalRW{
		abstract public static function exists($name);
		abstract public static function can($name, $stat=null);
		abstract public static function realName($name);
		abstract public static function authName();
		abstract public static function authStatus();
		abstract public static function authHas();
		public static function authHasnt(){
                       return(!(static::authHas()));
                }
		abstract public static function sudo($user);
		abstract public static function status($user);
		abstract public static function unsudo();
		abstract public static function isClever($name=null);
	}
}
