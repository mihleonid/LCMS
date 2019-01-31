<?php
namespace LCMS\Core{
	abstract class IPattern{
		abstract public sattic function get($s);
		abstract public static function getCMS();
		abstract public static function getReal($pattern);
	}
}
?>
