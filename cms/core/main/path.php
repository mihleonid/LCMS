<?php
namespace LCMS\Core{
	final class Path{
		const MAX_FILESIZE=1073741824;//1Gb=1024Mb=1048576Kb=1073741824b
		//private:
		private static $changes=array();
		private static $droot=null;
		private static $nreg=null;
		private static function root($path=""){
			if(static::$droot==null){
				static::initialize();
			}
			return rtrim((static::$droot)."/".static::abs($path), "/");
		}
		private static function uput($path, $contents){
			$path=static::root($path);
			if(is_dir($path)){
				throw new FileSystemException("It is a dir");
			}
			$dir=dirname($path);
			if(!file_exists($dir)){
				mkdir($dir, 0777, true);
			}
			return(file_put_contents($path, $contents)!==false);
		}
		private static function uget($path){
			$path=static::root($path);
			if(!is_file($path)){
				return "";
			}
			return file_get_contents($path);
		}
		private static function udelete($path){
			$path=static::root($path);
			if(is_file($path)){
				unlink($path);
				return true;
			}
			return false;
		}
		private static function uscan($path){
			return array_diff(scandir($path), array('.','..'));
		}
		private static function urmdir($path){
			$path=static::root($path);
			if(!is_dir($path)){
				return false;
			}
			$list=static::uscan($path);
			if(isset($list[0])){
				return false;
			}
			return rmdir($path);
		}
		private static function umkdir($path, $recursive=false){
			$path=static::root($path);
			if(!file_exists($path)){
				mkdir($path, 0777, $recursive);
				return true;
			}else{
				if(!is_dir($path)){
					throw new FileSystemException("Is not a dir");
				}
			}
			return false;
		}
		private static function unonregister(){
			$met=rnd(0, 1000).rnd(0, 1000).rnd(0, 1000);
			$path1=("register".$met.".check");
			$path2=("reGister".$met.".check");
			static::udelete($path1);
			static::udelete($path2);
			static::uput($path1, "check");
			if(static::uget($path2)=="check"){
				static::udelete($path1);
				return true;
			}
			static::udelete($path1);
			return false;
		}
		private static function nonregister(){
			if(static::$nreg==null){
				static::$nreg=static::unonregister();
			}
			return static::$nreg;
		}
		private static function nureal($path=""){
			$path=realpath($path);
			if($path==false){
				return false;
			}else{
				$path=rtrim(str_replace("\\", "/", $path), "/");
				if(static::nonregister()){
					$path=strtolower($path);
				}
				return $path;
			}
		}
		private static function ureal($path=""){
			$path=realpath(static::root($path));
			if($path==false){
				return false;
			}else{
				$path=rtrim(str_replace("\\", "/", $path), "/");
				if(static::nonregister()){
					$path=strtolower($path);
				}
				return $path;
			}
		}
		private static function uis_dir($path){return(is_dir(static::root($path)));}
		private static function uis_file($path){return(is_file(static::root($path)));}
		private static function ufile_exists($path){return(file_exists(static::root($path)));}
		private static function uappend($file, $content, $separator=""){
			$last=static::uget($file);
			if($last==""){
				$last=$content;
			}else{
				$last=$last.$separator.$content;
			}
			return static::uput($file, $last);
		}
		private static function uis_up($path){return(is_uploaded_file(static::root($path)));}
		private static function ufilesize($path){
			$path=static::root($path);
			if(!file_exists($path)){
				return 0;
			}
			return filesize($path);
		}
		//public:
		public static function append($file, $content, $separator=""){
			$last=static::get($file);
			if($last==""){
				$last=$content;
			}else{
				$last=$last.$separator.$content;
			}
			return static::put($file, $last);
		}
		public static function cpy($from, $to){return static::put($to, static::get($from));}
		public static function mov($from, $to){
			$to=static::put($to, static::get($from));
			static::delete($from);
			return $to;
		}
		public static function mov_up($from, $to){
			if(static::uis_up($from)){
				static::mov($from, $to);
			}
		}
		public static function delete($path){
			$content=static::uget($path);
			$res=static::udelete($path);
			if($res==false){
				static::uput($path, $content);
				return false;
			}
			static::$changes[]=array($path, $content);
			return true;
		}
		public static function put($path, $contentu){
			if(strlen($contentu)>static::MAX_FILESIZE){
				if(!Loc::get("agressive", true)){
					throw new FileSystemException("Too large");
				}
				$contentu=substr($contentu, 0, static::MAX_FILESIZE);
			}
			$content=static::uget($path);
			$res=static::uput($path, $contentu);
			if($res==false){
				static::uput($path, $content);
				return false;
			}
			static::$changes[]=array($path, $content);
			return true;
		}
		public static function rm($path) {
			if(static::uis_dir($path)){
				$files=static::scan($path);
				foreach($files as $file){
					$current=static::concat($path, $file);
					if(static::uis_dir($current)){
						rm($current);
					}else{
						$content=static::uget($current);
						$res=udelete($current);
						if(!$res){
							static::uput($current, $content);
							return false;
						}
						static::$changes[]=array($current, $content);
					}
				}
				return urmdir($path);
			}
			return false;
		}
		public static function scan($path){return static::uscan(static::root($path));}
		public static function get($path){
			if(static::ufilesize($path)>static::MAX_FILESIZE){
				if(!Loc::get("agressive", true)){
					throw new FileSystemException("Too large");
				}
				return "";#todo read part
			}
			return static::uget($path);
		}
		public static function getlines($path){
			$str=static::get($path);
			$str=str_replace("\r\n", "\n", $str);
			$str=str_replace("\r", "\n", $str);
			return explode("\n", $str);
		}
		public static function fatal($content){static::uappend("fatal.log", $content, "\r\n");}
		public static function tmp($path){return(static::cmsinstall(static::concat("tmp", $path)));}
		public static function tmpfile($prefix="tmp"){
			$prefix=strip($prefix);
			if($prefix==""){
				$prefix="tmp";
			}
			$i=0;
			while(static::ufile_exists(static::tmp($prefix.$i.".tmp"))){
				++$i;
			}
			return static::tmp($prefix.$i.".tmp");
		}
		public static function deleteroot($path){
			$root=static::ureal();
			if($root==false){
				throw new FileSystemException("No root");
				return false;
			}
			$path=str_replace("\\", "/", $path);
			$tmppath="";
			$newpath="";
			$found=false;
			foreach(explode('/', $path) as $part) {
				if($found){
					$newpath.=$part."/";
				}else{
					$tmppath.=$part."/";
					if(static::nureal($tmppath)==$root){
						$found=true;
					}
				}
			}
			if($found){
				$path=$newpath;
			}
			return static::abs($path);
		}
		public static function concat($a, $b){return static::abs(((static::abs($a))."/".(static::abs($b))));}
		public static function abs($path) {
			$path=trim(str_replace("\\", "/", $path), '/');
			$a=array();
			foreach(explode('/', $path) as $part) {
				$part=trim($part);
				if(empty($part)or($part==='.')){
					continue;
				}
				if($part!=='..') {
					array_push($a, $part);
				}elseif(count($a)>0){
					array_pop($a);
				}
			}
			return implode('/', $a);
		}
		public static function iabs($path){
			$path=static::abs($path);
			if(static::nonregister()){
				return(strtolower($path));
			}
			return $path;
		}
		public static function cms($path){return(static::cmsinstall(static::concat("cms", $path)));}
		public static function site($path){
			$path=static::abs($path);
			if(static::nonregister()){
				$path=strtolower($path);
			}
			foreach(explode('/', $path) as $part) {
				if($part=="cms"){
					throw new SecurityException("Access to cms");
				}
			}
			return $path;
		}
		public static function recovery($path){return(static::cmsinstall(static::concat("recovery", $path)));}
		public static function cmsinstall($path){return(static::concat((static::deleteroot(dirname(dirname(dirname( __FILE__ ))))), $path));}
		public static function sinclude($path){
			if(static::ufile_exists($path)){
				static::inc($path);
				return true;
			}
			return false;
		}
		public static function rsinclude($path){
			if(static::ufile_exists($path)){
				return static::inc($path);
			}
			return Pool::CRASH;
		}
		public static function inc($path){return(include(static::root($path)));}
		public static function exists($path){return(trim(static::get($path))!="");}
		public static function web($path){return("/".(static::abs($path)));}
		//handler:
		public static function initialize(){
			static::$droot=rtrim(str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']), "/");
			return true;
		}
		public static function test(){
			//$info
			//todo notest
		}
		public static function shutdown(){
			if(Pool::getFlushBack()){
				foreach(static::$changes as $ch){
					static::uput($ch[0], $ch[1]);
				}
			}
			Pool::caught();
		}
		public static function cleanup($dir="/"){
			clearstatcache(true);
			$files=static::scan($dir);
			foreach($files as $file){
				$current=static::concat($dir, $file);
				if(static::uis_dir()){
					static::cleanup($current);
					static::urmdir($current);
				}else{
					if(trim(static::uget($current))==""){
						static::udelete($current);
					}
				}
			}
		}
	}
}
?>
