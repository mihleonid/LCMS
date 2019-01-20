<?php
namespace LCMS\Core{
	function shutdown(){
		Pool::setCwd();
		Loc::shutdown();
		Handler::shutdown();
		ob_super_end_flush();
		exit;
	}
	function extshutdown(){
		Path::shutdown();
	}
	function cleanup(){
		Pool::setCwd();
		Path::cleanup();
		INI::cleanup();
		Handler::cleanupSelf();
		Handler::cleanup();
		Path::cleanup();
	}
	function test(){
		Pool::setCwd();
		Path::test();
		INI::test();
		Handler::testSelf();
		Handler::test();
	}
	function errHand($errno, $errstr, $errfile, $errline){
		Pool::setCwd();
		Handler::err(array($errno, $errstr, $errfile, $errline));
	}
	function excHand($exc){
		Pool::setCwd();
		Handler::exc(array($exc));
	}
	function initialize(){
		ob_implicit_flush(0);
		ob_start();
		Pool::initialize();
		Path::initialize();
		INI::initialize();
		Handler::initialize();
		set_error_handler("\\LCMS\\Core\\errHand");
		set_exception_handler("\\LCMS\\Core\\excHand");
		register_shutdown_function("\\LCMS\\Core\\extshutdown");
		$content=ob_get_clean();
		if(strlen($content)!=0){
			Path::fatal($content);
		}
		ob_start();
	}
}
?>