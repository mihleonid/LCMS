<?php
namespace LCMS\Core\Security{
	use \LCMS\Core\Result;
	use \LCMS\Core\Data;
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
	class Hash{
		const MD=1;
		const HMD=2;
		const PASS=3;
		const PLAIN=4;
		private static $mathod=null;
		private static $cost=null;
		public static function getCost(){
			if(Hash::$cost==null){
				Hash::$cost=Loc::get("cost");
			}
			return Hash::$cost;
		}
		public static function getMethod(){
			return Hash::PLAIN;#todo do
			if(Hash::$mathod==null){
				if(function_exists("password_hash")and function_exists("password_verify")and function_exists("password_needs_rehash") and function_exists("password_get_info")){
					Hash::$mathod=Hash::PASS;
					return Hash::PASS;
				}else{
					if(function_exists("hash")){
						Hash::$mathod=Hash::HMD;
						return Hash::HMD;
					}else{
						if(function_exists("md5")){
							Hash::$mathod=Hash::MD;
							return Hash::MD;
						}else{
							Hash::$mathod=Hash::PLAIN;
							return Hash::PLAIN;
						}
					}
				}
			}else{
				return Hash::$mathod;
			}
		}
		public static function verify($pass, $hash){
			$method = Hash::getMethod();
			if($method==Hash::MD){
				return(trim(md5($pass))==trim($hash));
			}
			if($method==Hash::HMD){
				return(trim(hash('md5', $pass))==trim($hash));
			}
			if($method==Hash::PASS){
				return password_verify($pass, $hash);
			}
			return(trim($pass)==trim($hash));
			#php_bug cant use switch because unknown reason
			/*switch(Hash::getMethod()){
				case Hash::MD:
					return(trim(md5($pass))==trim($hash));
					break;
				case Hash::HMD:
					return(trim(hash('md5', $pass))==trim($hash));
					break;
				//case (Hash::PASS):
					return password_verify($pass, $hash);
					break;
				case Hash::PLAIN:
				default:
					return(trim($pass)==trim($hash));
					break;
			}*/
		}
		public static function make($password){
			switch(Hash::getMethod()){
				case Hash::MD:
					return(md5($password));
				case Hash::HMD:
					return(hash('md5', $password));
				case Hash::PASS:
					return(password_hash($password, PASSWORD_DEFAULT, array('cost'=>Hash::getCost())));
				case Hash::PLAIN:
				default:
					return($password);
			}
		}
		public static function rehash($password, $hash){
			if(Hash::getMethod()==Hash::PASS){
				if(password_needs_rehash($hash, PASSWORD_DEFAULT, array('cost'=>Hash::getCost()))){
					return password_hash($password, PASSWORD_DEFAULT, array('cost'=>Hash::getCost()));
				}
			}
			return false;
		}
	}
	class ELog{
		public static function clear(){
			$size=0;
			if(file_exists($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php")){
				unlink($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php");
			}
			$status=ELog::statuses("set", $size);
			return Loc::set("error", $status.":".$size);
		}
		public static function deleteLine($n){
			$file=file($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php");
			unset($file[$n]);
			$file=trim(implode('', $file));
			if($file==""){
				unlink($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php");
				$size=0;
			}else{
				file_put_contents($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php", $file);
				$size=filesize($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php");
			}
			$status=ELog::statuses("set", $size);
			return Loc::set("error", $status.":".$size);
		}
		public static function setSize($mode){
			return Loc::set("elogmaxsize", $mode);
		}
		public static function reCount(){
			$size=filesize($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php");
			$status=ELog::statuses("set", $size);
			return Loc::set("error", $status.":".$size);
		}
		protected static function statuses($act="set", $status="empty"){
			if($act=="get"){
				switch($status){
					default:
					case "empty":
						$size=0;
						break;
					case "some":
						$size=1;
						break;
					case "big enough":
						$size=501;
						break;
					case "big":
						$size=1025;
						break;
					case "too big":
						$size=6025;
						break;
					case "large enough":
						$size=10025;
						break;
					case "large":
						$size=56025;
						break;
					case "too large":
						$size=186025;
						break;
					case "extra large":
						$size=686025;
						break;
					case "some else and fatal":
						$size=1000001;
						break;
					case "critical":
						$size=1048576;
						break;
				}
				return $size;
			}else{
				$size=$status;
				if($size==0){
					$status="empty";
				}
				if($size>0){
					$status="some";
				}
				if($size>500){
					$status="big enough";
				}
				if($size>1024){
					$status="big";
				}
				if($size>6024){
					$status="too big";
				}
				if($size>10024){
					$status="large enough";
				}
				if($size>56024){
					$status="large";
				}
				if($size>186024){
					$status="too large";
				}
				if($size>686024){
					$status="extra large";
				}
				if($size>1000000){
					$status="some else and fatal";
				}
				if($size>1048576){
					$status="critical";
				}
				return $status;
			}
		}
		public static function Logged($msg){
			if(!file_exists($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php")){
				$maxsize=Loc::get("elogmaxsize");
				if($maxsize==null){
					$maxsize="large";
					Loc::set("elogmaxsize", $maxsize);
				}
				if($maxsize!="empty"){
					file_put_contents($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php", $msg);
				}
				$size=strlen($msg);
				$status=ELog::statuses("set", $size);
				return Loc::set("error", $status.":".$size);
			}
			$size=filesize($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php");
			$status=ELog::statuses("set", $size);
			$r=Loc::set("error", $status.":".$size);
			if($size+1<ELog::statuses("get", Loc::get("elogmaxsize"))){
				$fh=fopen($_SERVER['DOCUMENT_ROOT']."/cms/error_log.php", "a+");
				fwrite($fh, "\r\n".$msg);
				fclose($fh);
			}
			return $r;
		}
	}
	class AntiXSS{
		public static function U(){
			$u=@header("Access-Control-Allow-Origin: http://www.nt10.ru", true);
			@header("Allow-Origin: http://www.nt10.ru", true);
			@header("Access-Control-Allow-Credentials: false", true);
			@header("Vary: Origin", true);
			$r=new Result();
			if(!$u){
				$r->addError("Невозможно отправить заголовки");
			}
			return $r;
		}
		public static function S(){
			$u=AntiXSS::U();
			@header("X-Frame-Options: sameorigin;", true);
			@header("X-Frame-Options: Sameorigin;", false);
			@header("x-xss-protection: 1; mode=block", true);
			@header("x-frame-options: SAMEORIGIN", true);
			@header("x-frame-options: sameorigin", false);
			return $u;
		}
		public static function H(){
			$a=AntiXSS::U();
			@header("X-Frame-Options: deny;", true);
			@header("X-Frame-Options: Deny;", false);
			@header("x-xss-protection: 1; mode=block", true);
			@header("x-frame-options: DENY", true);
			@header("x-frame-options: deny", false);
			return $a;
		}
		public static function R(){
			if(!isset($_SERVER['HTTP_REFERER'])){
				AntiXSS::hacker();
			}
			if(!isset($_SERVER['HTTP_HOST'])){
				AntiXSS::hacker();
			}
			if(!preg_match("@https?://".$_SERVER['HTTP_HOST']."/cms/.*@", $_SERVER['HTTP_REFERER'])){
				AntiXSS::hacker();
			}
			return new Result();
		}
		public static function hacker(){
			if((!class_exists("\\LCMS\\MainModules\\Protector"))||(!\LCMS\MainModules\Protector::mode())){
				Elog::Logged("[HACKER ATTACK] from: ".((isset($_SERVER['HTTP_REFERER']))?($_SERVER['HTTP_REFERER']):("UNKNOWN")).", to:".((isset($_SERVER['PHP_SELF']))?($_SERVER['PHP_SELF']):("UNKNOWN")).", in:".date("H:i:s").'&nbsp;'.date("d.m.Y"));
				die("HACKER ATTACK! YOU ARE LOGGED AND SEEKED!");
			}else{
				exit;
			}
		}
	}
	#region SecureMethods
	class Locker{
		public static function setA($deactive){
			return Loc::set("locker", $deactive);
		}
		public static function getA(){
			return Loc::get("locker");
		}
		public static function resetA(){
			return Locker::setA(!Locker::getA());
		}
		public static function set(){
			if(Locker::getA()){
				return true;
			}
			if(!Loc::exists("lock")){
				Loc::set("lock", time());
				return true;
			}
			$con=Loc::get("lock");
			if(($con==null)or(($con+120)<time())){
				Loc::set("lock", time());
				return true;
			}
			return false;
		}
		public static function unlock(){
			return Loc::set("lock", null);
		}
	}
	class Salt{
		public static function setA($deactive){
			return Loc::set("salt", $deactive);
		}
		public static function getA(){
			return Loc::get("salt");
		}
		public static function resetA(){
			return Salt::setA(!Salt::getA());
		}
		public static function get(){
			return Loc::get("z");
		}
		public static function change($s){
			static::set($s);
		}
		public static function set($stepen){
			$step=intval($stepen);
			$step=min($step, 1000);
			$step=max($step, 1);
			Loc::set("step", $step);
			$str="";
			for($i=0;$i<$step;$i++){
				$str.=md5(mt_rand());
			}
			return Loc::set("z", $str);
		}
		public static function compare($a){
			if(Salt::getA()){
				return true;
			}
			return(trim($a)==Salt::get());
		}
	}
	class Counter{
		public static function setA($deactive){
			return Loc::set("counter", $deactive);
		}
		public static function getA(){
			return Loc::get("counter");
		}
		public static function resetA(){
			return Counter::setA(!Counter::getA());
		}
		public static function clear(){
			return Loc::set("uncodeform", 1);
		}
		public static function get($check=false){
			if($check){
				if(Counter::getA()){
					return true;
				}
				$int=Loc::get("uncodeform");
				$int=Counter::plus($int);
				if($check==$int){
					Loc::set("uncodeform", Counter::plus($int));
					return true;
				}else{
					return false;
				}
			}else{
				$int=Loc::get("uncodeform");
				return Counter::plus($int);
			}
		}
		private static function maxInt(){
			$int=intval((PHP_INT_MAX-1000)/20-15);
			$mod=($int%2);
			return($int+$mod+1);
		}
		private static function plus($int){
			$int=(($int+1) % ( Counter::maxInt() ));
			if($int==0){
				$int=2;
			}
			return $int;
		}
	}
	#endregion
}
?>
