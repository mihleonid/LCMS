<?php
namespace LCMS\Core{
	function b64($text);	//base64
	function ub64($text);	//decode 64

	function code($a);			//serialize
	function uncode($a, $def=Pool::CRASH);	//unserialize
	
	function bitmask($mask, $bitint);	//compare mask by bitint

	tobool($a);
	bool2string($a);
	bool2str($a);
	bool2int($a);
	str2data($a);	//textual seria
	data2str($a);
	arr2str($a);
	str2arr($a);

	nop();
	function rnd($a=null, $b=null);
	function is_class_of($a, $b);	//is a subclass or instance of b

	ll($t);	//return localized t
	ee($t);	//echo localized t

	function html($str);	//strip html

	function strip($a, $firstletter=true, $additional='', $isadditionalletter=false);	//convert string to cms-safe
	function strip_ru($a, $firstletter=false);						//with cyr
	function firstlettercondition($a);							//sysfunc

	function str_replace_once($search, $replace, $text);

	function ob_super_end_flush();	//all buffers
}

