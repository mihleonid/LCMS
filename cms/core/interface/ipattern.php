<?php
namespace LCMS\Core{
	abstract class IPattern{
		abstract public static function get($s);
		abstract public static function getCMS();
		abstract public static function getReal($pattern);
		abstract public static function canUs($status, $pattern);
	}
}
