<?php
namespace LCMS\Core{
	abstract class IAllowedTags{
		abstract public static function getAllowedTags($can=null);
		abstract public static function addHTag($tag);
		abstract public static function addHAttr($tag, $attr);
		abstract public static function deleteH($path);
		abstract public static function addTag($tag);
		abstract public static function addAttr($tag, $attr);
		abstract public static function delete($path);
	}
}
?>
