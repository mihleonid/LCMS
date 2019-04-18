<?php
namespace LCMS\Core{
	class Encoder{
		public static function encode($str, $pass){
			$len=strlen($str);
			$p=strlen($pass);
			$str=unpack('c*', $str);
			$pass=unpack('c*', $pass);
			$newstr="";
			for($i=0;$i<$len;$i++){
				$newstr.=pack('c', (($str[$i+1]+$pass[($i%$p)+1])%256)^($pass[($i%$p)+1]));
			}
			return "e".base64_encode($newstr);
		}
		public static function decode($str, $pass){
			$str=substr($str, 1);
			$str=base64_decode($str);
			$str=unpack('c*', $str);
			$pass=unpack('c*', $pass);
			$newstr="";
			$len=count($str);
			$p=count($pass);
			for($i=0;$i<$len;$i++){
				$newstr.=pack('c', (($str[$i+1]^$pass[($i%$p)+1])-$pass[($i%$p)+1]+256)%256);
			}
			return $newstr;
		}
	}
}

