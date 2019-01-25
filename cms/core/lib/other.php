<?php
namespace LCMS\Core{
	function nop(){}
	function rnd($a=null, $b=null){
		if($a===null){
			$a=0;
		}
		if($b===null){
			$b=mt_getrandmax();
		}
		return mt_rand($a, $b);
	}
	function is_class_of($a, $b){
		$aa=null;
		if(!is_string($a)){
			$aa=get_class($a);
		}
		$bb=null;
		if(!is_string($b)){
			$bb=get_class($b);
		}
		if($aa==$bb){
			return true;
		}
		returm is_subclass_of($a, $b);
	}
}
?>