<?php
namespace LCMS\Core{
	class CMS{
		public static function shutdown(){
			Pool::setCwd();
			Loc::shutdown();
			Handler::shutdown();
			ob_super_end_flush();
			exit;
		}
		public static function extshutdown(){
			Path::shutdown();
		}
		public static function cleanup(){
			Pool::setCwd();
			Path::cleanup();
			INI::cleanup();
			Handler::cleanupSelf();
			Handler::cleanup();
			Path::cleanup();
		}
		public static function test(){
			Pool::setCwd();
			Path::test();
			INI::test();
			Handler::testSelf();
			Handler::test();
		}
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
			Pool::initialize();
			Path::initialize();
			INI::initialize();
			Handler::initialize();
			set_error_handler("\\LCMS\\Core\\CMS::errHand");
			set_exception_handler("\\LCMS\\Core\\CMS::excHand");
			register_shutdown_function("\\LCMS\\Core\\CMS::extshutdown");
			spl_autoload_register("\\LCMS\\Core\\CMS::autoload");
			$content=ob_get_clean();
			if(strlen($content)!=0){
				Path::fatal($content);
			}
			ob_start();
		}
		public static function autoload($c){
			$conf=new Config(Path::cms("stdclass.config"));
			$line=$conf->get($c);
			$def=$c;
			$sim=$c;
			if($line!=""){
				$t=explode(' ', $line);
				if(isset($t[2])){
					$c=$t[0];
					$def=$t[1];
					$iface=$t[2];
					if(!is_class_of($c, $iface)){
						#tofo cfg
					}
				}
			}
		}
		protected static function classpath($c){
			$c=strtolower($c);
			$c=explode("\\", trim($c, "\\"));
			if($c[0]=="lcms"){
				if(isset($c[1])){
					if($c[1]=='core'){
						return array(Path::cms("core"), Path::cms("exceptions"), Path::cms("interface"));
					}
				}
			}
			return array();
		}
		protected static function loadin($dir, $c){
			Path::sinclude(Path::concat($dir, strtolower(strip($c))));
		}
	}
}
?>