<?php
namespace LCMS\Core{
	class ELog{
		private static function path(){
			return Path::cms("error_log.log");
		}
		public static function clear(){
			$size=0;
			Path::delete(static::path());
			$status=static::statuses("set", $size);
			return Loc::set("error", $status.":".$size);
		}
		public static function deleteLine($n){
			$cnt=Path::get(static::path());
			$arr=explode("\n", $cnt);
			if(isset($arr[$n])){
				unset($arr[$n]);
			}
			$cnt=implode("\n", $arr);
			Path::put(static::path(), $cnt);
			$size=strlen($cnt);
			$status=static::statuses("set", $size);
			return Loc::set("error", $status.":".$size);
		}
		public static function setSize($mode){
			return Loc::set("elogmaxsize", $mode);
		}
		public static function update(){
			$size=strlen(Path::get(static::path()));
			$status=static::statuses("set", $size);
			Loc::set("error", $status.":".$size);
		}
		protected static function statuses($act="set", $status="empty"){
			$size=-1;
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
		public static function logged($msg){
			if(!Path::exists(static::path())){
				$maxsize=Loc::get("elogmaxsize", null);
				if($maxsize==null){
					$maxsize="large";
					Loc::set("elogmaxsize", $maxsize);
				}
				if($maxsize!="empty"){
					Path::put(static::path(), $msg);
				}
				$size=strlen($msg);
				$status=static::statuses("set", $size);
				return Loc::set("error", $status.":".$size);
			}
			$msg=Path::get(static::path())."\r\n".$msg;
			$size=strlen($msg);
			if($size+1<static::statuses("get", Loc::get("elogmaxsize"))){
				$status=static::statuses("set", $size);
				Loc::set("error", $status.":".$size);
				Path::put(static::path(), $msg);
			}
		}
	}	
}

