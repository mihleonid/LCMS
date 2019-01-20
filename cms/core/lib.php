<?php
namespace LCMS\Core{
	function strip($a, $firstletter=true){
		$a=preg_replace('@[^a-zA-Z_1-90\-\~]@', '', $a);
		if($firstletter){
			while(!firstlettercondition($a[0])){
				$a=substr($a, 1);
			}
		}
		return $a;
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
	function b64($text){
		$text=base64_encode($text);
		$text=strtr($text, "+/=", "_-~");
		return $text;
	}
	function ub64($text){
		$text=strtr(trim($text), "_-~", "+/=");
		return base64_decode($text);
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
	function tobool($a){
		if($a===true){
			return true;
		}
		if($a===false){
			return false;
		}
		if($a=="1"){
			return true;
		}
		if($a=="0"){
			return false;
		}
		if(strtolower($a)=="on"){
			return true;
		}
		if(strtolower($a)=="yes"){
			return true;
		}
		if(strtolower($a)=="off"){
			return false;
		}
		if(strtolower($a)=="no"){
			return false;
		}
		if($a){
			return true;
		}
		return false;
	}
	function bool2string($b){
		if($b){
			return "1";
		}else{
			return "0";
		}
	}
	function bitmask($mask, $bitint){
		return(($mask&$bitint)==$bitint);
	}
	function bool2int($b){
		if($b){
			return 1;
		}else{
			return 0;
		}
	}
	function code($a){
		return serialize($a);
	}
	function str_replace_once($search, $replace, $text) {
		$pos = strpos($text, $search);
		return (($pos!==false)?(substr_replace($text, $replace, $pos, strlen($search))):($text));
	}
	function nop(){}
	function uncode($a, $def=Pool::CRASH){
		set_error_handler('nop');
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
	function ob_super_end_flush(){
		set_error_handler('nop');
		while (@ob_end_flush());
		restore_error_handler();
	}
	function rnd($a=null, $b=null){
		if($a===null){
			$a=0;
		}
		if($b===null){
			$b=mt_getrandmax();
		}
		return mt_rand($a, $b);
	}
	function str2data($str){
		return uncode(ub64($str), array());
	}
	function data2str($arr){
		return b64(code($arr));
	}
	function bool2str($a){
		return bool2string($a);
	}
	public function str2arr($str){
		if($str==''){
			return array();
		}
		$str=explode('|', $str);
		$DATA=array();
		$tmpcount=count($str);
		for($i=0;$i<$tmpcount;++$i){
			if(!isset($str[$i+1])){
				break;
			}
			$DATA[$str[$i]]=$str[$i+1];
			++$i;
		}
		return $DATA;
	}
	public function arr2str($data){
		if(!is_array($data)){
			return '';
		}
		$str='';
		foreach($data as $k->$v){
			$k=preg_replace('@[^a-zA-Z_]@', '', $k);
			$l=preg_replace("@[^a-zA-Z_а-яА-Яё1-90, \.\?]@u", '', $l);
			$str.='|'.$k.'|'.$v;
		}
		return ltrim($str, '|');
	}
}
?>