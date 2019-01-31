<?php
namespace LCMS\Core{
	abstract public class IUser extends GlobalRW{
		abstract public static function exists($name);
		abstract public static function can($name, $stat);
		abstract public static function realName($name);
		abstract public static function authName();
		abstract public static function authStatus();
		abstract public static function authHas();
		abstract public static function sudo($user);
		abstract public static function unsudo();
	}
}
?>
