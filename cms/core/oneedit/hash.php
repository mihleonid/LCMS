<?php
namespace LCMS\Core{
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
}

