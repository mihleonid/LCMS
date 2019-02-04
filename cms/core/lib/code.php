<?php
namespace LCMS\Core{
	function b64($text){
		$text=base64_encode($text);
		$text=strtr($text, "+/=", "_-~");
		return $text;
	}
	function ub64($text){
		$text=strtr(trim($text), "_-~", "+/=");
		return base64_decode($text);
	}
	function code($a){
		return serialize($a);
	}
	function uncode($a, $def=Pool::CRASH){
		set_error_handler('\\LCMS\\Core\\CMS::errNo');
		$b=@unserialize($a);
		if(($b===false)and($a!=serialize(false))){
			if($def==Pool::CRASH){
				$b=Pool::CRASH;
			}else{
				$b=$def;
			}
		}
		restore_error_handler();
		return $b;
	}
	function bitmask($mask, $bitint){
		return(($mask&$bitint)==$bitint);
	}
}
?>
