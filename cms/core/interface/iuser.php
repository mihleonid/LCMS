<?php
namespace LCMS\Core{
	abstract public class IUser extends GlobalRW{
		abstract public static function exists($name);
		abstract public static function can($name, $stat);
		abstract public static function realName($name);
		abstract public static function authName();
	}
}
?>