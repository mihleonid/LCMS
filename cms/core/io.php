<?php
namespace LCMS\Core{
	final class IO{
		private static function httpget($path, $data=array()){
			if(!is_array($data)){
				$data=array();
			}
			$data['method']="GET";
			if(isset($data['content'])){
				unset($data['content']);
			}
			return file_get_contents($path, false, stream_context_create(array('http'=>$data)));
		}
		private static function httpput($path, $content, $data=array()){
			if(!is_array($data)){
				$data=array();
			}
			$data['method']="POST";
			$data['content']=$content;
			return file_get_contents($path, false, stream_context_create(array('http'=>$data)));
		}
		public static function get($path, $data=array()){
			if(!is_array($data)){
				$data=array();
			}
			$protocol=strtolower(substr($path."nnnnnnnn", 0, 8));
			if(($protocol[0]=='h')and($protocol[1]=='t')and($protocol[2]=='t')and($protocol[3]=='p')){
				if((($protocol[4]=='s')and($protocol[5]==':')and($protocol[6]=='/')and($protocol[7]=='/'))or(($protocol[4]==':')and($protocol[5]=='/')and($protocol[6]=='/'))){
					return static::httpget($path, $data);
				}
			}
			if(($protocol[0]=='f')and($protocol[1]=='i')and($protocol[2]=='l')and($protocol[3]=='e')and($protocol[4]==':')and($protocol[5]=='/')and($protocol[6]=='/')){
				return Path::get(substr($path, 7));
			}
			if(($protocol[0]=='i')and($protocol[1]=='o')and($protocol[2]==':')and($protocol[3]=='/')and($protocol[4]=='/')){
				$datan=substr($path, 5);
				$datan=explode("%%", $datan);
				$path=$datan[0];
				$data=array();
				$tmpcount=count($datan)
				for($i=1;$i<$tmpcount;++$i){
					$cur=$datan[$i];
					$cur=explode('=', $cur);
					if(!isset($cur[1])){
						$cur[1]="";
					}
					$data[$cur[0]]=$cur[1];
				}
				return IO::get($path, $data);
			}
			if(($protocol[0]=='d')and($protocol[1]=='a')and($protocol[2]=='t')and($protocol[3]=='a')and($protocol[4]==':')and($protocol[5]=='/')and($protocol[6]=='/')){
				if(!isset($data['def'])){
					$data['def']=array();
				}
				if(!isset($data['module'])){
					$data['module']=null;
				}
				return Data::get($data['module'], substr($path, 7), $data['def']);
			}
			if(($protocol[0]=='l')and($protocol[1]=='o')and($protocol[2]=='c')and($protocol[3]==':')and($protocol[4]=='/')and($protocol[5]=='/')){
				if(!isset($data['def'])){
					$data['def']=null;
				}
				return Loc::get(substr($path, 6), $data['def']);
			}
			if(($protocol[0]=='i')and($protocol[1]=='n')and($protocol[2]=='i')and($protocol[3]==':')and($protocol[4]=='/')and($protocol[5]=='/')){
				if(isset($data['tmp'])){
					return ini_get(substr($path, 6));
				}
				return INI::get(substr($path, 6));
			}
			return Path::get($path);
		}
		public static function put($path, $content, $data=array()){
			if(!is_array($data)){
				$data=array();
			}
			$protocol=strtolower(substr($path."nnnnnnnn", 0, 8));
			if(($protocol[0]=='h')and($protocol[1]=='t')and($protocol[2]=='t')and($protocol[3]=='p')){
				if((($protocol[4]=='s')and($protocol[5]==':')and($protocol[6]=='/')and($protocol[7]=='/'))or(($protocol[4]==':')and($protocol[5]=='/')and($protocol[6]=='/'))){
					return static::httpput($path, $content, $data);
				}
			}
			if(($protocol[0]=='f')and($protocol[1]=='i')and($protocol[2]=='l')and($protocol[3]=='e')and($protocol[4]==':')and($protocol[5]=='/')and($protocol[6]=='/')){
				return Path::put(substr($path, 7), $content);
			}
			if(($protocol[0]=='i')and($protocol[1]=='o')and($protocol[2]==':')and($protocol[3]=='/')and($protocol[4]=='/')){
				$datan=substr($path, 5);
				$datan=explode("%%", $datan);
				$path=$datan[0];
				$data=array();
				$tmpcount=count($datan);
				for($i=1;$i<$tmpcount;++$i){
					$cur=$datan[$i];
					$cur=explode('=', $cur);
					if(!isset($cur[1])){
						$cur[1]="";
					}
					$data[$cur[0]]=$cur[1];
				}
				return IO::put($path, $content, $data);
			}
			if(($protocol[0]=='d')and($protocol[1]=='a')and($protocol[2]=='t')and($protocol[3]=='a')and($protocol[4]==':')and($protocol[5]=='/')and($protocol[6]=='/')){
				if(!isset($data['module'])){
					$data['module']=null;
				}
				return Data::put($data['module'], substr($path, 7), $content);
			}
			if(($protocol[0]=='l')and($protocol[1]=='o')and($protocol[2]=='c')and($protocol[3]==':')and($protocol[4]=='/')and($protocol[5]=='/')){
				if(isset($data['tmp'])){
					return ini_set(substr($path, 6), $content);
				}
				return Loc::put(substr($path, 6), $content);
			}
			if(($protocol[0]=='i')and($protocol[1]=='n')and($protocol[2]=='i')and($protocol[3]==':')and($protocol[4]=='/')and($protocol[5]=='/')){
				return INI::put(substr($path, 6), $content);
			}
			return Path::put($path, $content);
		}
		public static function exists($path, $data=array()){return(trim(static::get($path, $data))!="");}
	}
}
?>