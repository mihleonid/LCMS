<?php
namespace LCMS\Core{
	abstract class IMutex{
		abstract public static function set($module, $name);
		abstract public static function delete($module, $name);
	}
}
