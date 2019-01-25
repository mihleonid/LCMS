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
}
?>