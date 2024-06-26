<?php
namespace LCMS\Core{
	function html($str){
		//return(htmlentities($str, ENT_QUOTES|ENT_HTML5, "UTF-8")); low comparability
		return(htmlentities($str, ENT_QUOTES, "UTF-8"));
	}
	function htmlamp($str){
		return str_replace("&amp;", "&", html($str));
	}
	function strip($a, $firstletter=true, $additional='', $isadditionalletter=false){
		$a=preg_replace('@[^a-zA-Z_1-90\-\~'.$additional.']@', '', $a);
		if($firstletter){
			while((!firstlettercondition($a[0]))||((strpos($additional, $a[0])!==false)&&(!$isadditionalletter))){
				$a=substr($a, 1);
			}
		}
		return $a;
	}
	function strip_ru($a, $firstletter=false){
		$a=preg_replace('@[^a-zA-Zа-яА-ЯыёйцЫЁЙЦ_1-90\-\~]@u', '', $a);
		if($firstletter){
			while(!firstlettercondition($a[0])){
				$a=substr($a, 1);
			}
		}
		return $a;
	}
	function str_replace_once($search, $replace, $text){
		$pos = strpos($text, $search);
		return (($pos!==false)?(substr_replace($text, $replace, $pos, strlen($search))):($text));
	}
	function ob_super_end_flush(){
		set_error_handler('\\LCMS\\Core\\CMS::errNo');
		#todo exception
		while (@ob_end_flush());
		restore_error_handler();
	}
	function firstlettercondition($a){
		if($a=='1'){
			return false;
		}
		if($a=='2'){
			return false;
		}
		if($a=='3'){
			return false;
		}
		if($a=='4'){
			return false;
		}
		if($a=='5'){
			return false;
		}
		if($a=='6'){
			return false;
		}
		if($a=='7'){
			return false;
		}
		if($a=='8'){
			return false;
		}
		if($a=='9'){
			return false;
		}
		if($a=='0'){
			return false;
		}
		if($a=='_'){
			return false;
		}
		if($a=='-'){
			return false;
		}
		if($a=='~'){
			return false;
		}
		return true;
	}
}
