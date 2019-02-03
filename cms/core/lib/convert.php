<?php
namespace LCMS\Core{
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
	function bool2int($b){
		if($b){
			return 1;
		}else{
			return 0;
		}
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
	function str2arr($str){
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
	function arr2str($data){
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
