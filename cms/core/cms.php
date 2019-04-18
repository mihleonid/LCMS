<?php
namespace LCMS\Core{
	class CMS{
		public static function shutdown(){
			Pool::setCwd();
			Page::footer();
			Loc::shutdown();
			Handler::shutdown();
			ob_super_end_flush();
			exit;
		}
		public static function extshutdown(){
			Path::shutdown();
		}
		public static function update(){
			Localization::update();
		}
		public static function cleanup(){
			Pool::setCwd();
			Path::cleanup();
			INI::cleanup();
			Handler::cleanupSelf();
			Handler::cleanup();
			Localization::cleanup();
			Path::cleanup();
		}
		/* todo test
		public static function test(){
			Pool::setCwd();
			Path::test();
			INI::test();
			Handler::testSelf();
			Handler::test();
		}
		*/
		public static function errNo($errno, $errstr, $errfile, $errline){}
		public static function excNo($exc){}
		public static function errHand($errno, $errstr, $errfile, $errline){
			Pool::setCwd();
			Handler::err(array($errno, $errstr, $errfile, $errline));
		}
		public static function excHand($exc){
			Pool::setCwd();
			Handler::exc(array($exc));
		}
		public static function initialize(){
			ob_implicit_flush(0);
			ob_start();
			spl_autoload_register("\\LCMS\\Core\\CMS::autoload");
			Pool::initialize();
			Path::initialize();
			INI::initialize();
			Handler::initialize();
			//todo decomment
			//set_error_handler("\\LCMS\\Core\\CMS::errHand");
			//set_exception_handler("\\LCMS\\Core\\CMS::excHand");
			register_shutdown_function("\\LCMS\\Core\\CMS::extshutdown");
			$content=ob_get_clean();
			if(strlen($content)!=0){
				Path::fatal($content);
			}
			ob_end_clean();
		}
		public static function autoload($c){
			Pool::setCwd();
			$conf=new \LCMS\Core\Config(Path::cms("stdclass.config"));
			$line=$conf->get("\\".trim($c, "\\"));
			if($line!=""){
				$t=explode(' ', $line);
				if(isset($t[2])){
					$cl=$t[0];
					$def=$t[1];
					$iface=$t[2];
					static::loadincp($iface);
					static::loadincp($cl);
					if(!is_class_of($cl, $iface)){
						$cl=$def;
						static::loadincp($cl);
					}
					if(isset($t[3])){
						if(!is_class_of($cl, $iface)){
							$cl=$t[3];
						}
						static::loadincp($cl);
					}
					static::calias($cl, $c);
				}else{
					static::loadincp($c);
				}
			}else{
				static::loadincp($c);
			}
		}
		protected static function calias($a, $b){
			if(class_exists($b)){
				return false;
			}else{
				return class_alias($a, $b);
			}
		}
		protected static function loadincp($c){
			if(class_exists($c)){
				return true;
			}
			$e=explode("\\", trim($c, "\\"));
			return static::loadin(static::classpath($c), $e[count($e)-1]);
		}
		protected static function classpath($c){
			$c=strtolower($c);
			$c=explode("\\", trim($c, "\\"));
			if($c[0]=="lcms"){
				if(isset($c[1])){
					if($c[1]=='core'){
						return array(Path::cms("core"), Path::cms("core/interface"), Path::cms("core/exceptions"), Path::cms("core/oneedit"), Path::cms("core/lmodul"));
					}
				}
			}
			return array();
		}
		protected static function loadin($dir, $c){
			if(is_array($dir)){
				foreach($dir as $d){
					$r=static::loadin($d, $c);
					if($r){
						return true;
					}
				}
				return false;
			}
			return Path::sinclude(Path::concat($dir, strtolower(strip($c)).".php"));
		}
	}
}
