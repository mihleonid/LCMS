<?php
namespace LCMS\Core{
	//imp INI by Config
	class INI{
		private static function path(){
			return(Path::cms("config/php.ini"));
		}
		private static function def(){
			$file=static::path();
			if(!Path::exists($file)){
				Path::put($file, ";;;;;;;;;;;;;;;;;;;;;;;;;\r\n;Файл распространяется  ;\r\n;на CMS и сайт или, при ;\r\n;специальном подключении;\r\n;на любой скрипт.       ;\r\n;;;;;;;;;;;;;;;;;;;;;;;;;\r\nmemory_limit = 60090M\r\nmax_input_size = 1024M\r\nmax_execution_time = 60\r\ndefault_socket_timeout = 10\r\ndefault_charset = utf8\r\ndefault_charset = UTF8\r\ndefault_charset = utf-8\r\ndefault_charset = UTF-8\r\n; display_errors = Off;\r\n; log_errors = Off;\r\nuser_agent = CMSLeonid\r\ndisplay_errors = 1\r\nlog_errors = 1\r\n; comment");
			}
		}
		public static function initialize(){
			static::def();
			$file=Path::getlines(static::path());
			foreach($file as $line){
				$line=Code::deleteCommentLine($line, ';');
				if(strpos($line, "=")){
					$par=explode("=", $line);
					$par[0]=trim($par[0]);
					if(!isset($par[1])){
						$par[1]="";
					}
					$par[1]=trim($par[1]);
					ini_set($par[0], $par[1]);
				}
			}
		}
		public static function cleanup(){
			$path=static::path();
			static::def();
			$file=Path::getlines($path);
			$newfile=array();
			foreach($file as $line){
				$line=trim($line);
				if(strpos($line, ";")!==false){
					$newfile[]=$line;
					continue;
				}
				if(strpos($line, "=")==false){
					continue;
				}
				if(!in_array($line, $newfile)){
					$newfile[]=$line;
				}
			}
			$newfile=implode("\r\n", $newfile);
			Path::put($path, $newfile);
		}
		public static function set($name, $val){
			$file=static::path();
			static::def();
			$name=strip($name);
			$val=preg_replace("@^[a-zA-Z1-90\"']@", "", $val);
			Path::append($file, $name."=".$val,"\r\n");
			ini_set($name, $val);
			static::initialize();
		}
		public static function setFile($content){
			$content=Code::baseStyle($content);
			if($content==""){
				$content=";none";
			}
			Path::put(static::path(), $content);
		}
		public static function get($name){
			static::def();
			$name=trim($name);
			$file=file(static::path());
			foreach($file as $line){
				$line=Code::deleteCommentLine($line, ';');
				if(strpos($line, "=")){
					$par=explode("=", $line);
					$par[0]=trim($par[0]);
					if(!isset($par[1])){
						$par[1]="";
					}
					if($par[0]==$name){
						return trim($par[1]);
					}
				}
			}
			return ini_get($name);
		}
	}
}
