<?php
namespace LCMS\Core{
	abstract class IAllowedTags{
		abstract public function getAllowedTags($can=null);
		abstract public function addHTag($tag);
		abstract public function addHAttr($tag, $attr);
		abstract public function deleteH($path);
		abstract public function addTag($tag);
		abstract public function addAttr($tag, $attr);
		abstract public function delete($path);
	}
}
?>