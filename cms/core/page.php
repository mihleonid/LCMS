<?php
namespace LCMS\Core{
	class Page{
		private static $foot;
		public static function delete($path){
			$path=static::clearPath($path);
			if(Path::exists($path)){
				Path::delete($path);
				PageLog::put($path, User::authName(), PageLog::DELETE);
				return new Result();
			}else{
				PageLog::put($path, User::authName(), PageLog::DELETE, false);
				return new Result("---pagenotexists---");
			}
		}
		public static function deleteDir($path){
			$path=static::clearPath($path);
			$path=substr($path, 0, strlen($path)-4);//4==strlen(".php")
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path) and is_dir($_SERVER['DOCUMENT_ROOT'].$path)){
				$di=new \RecursiveDirectoryIterator($_SERVER['DOCUMENT_ROOT'].$path, \FilesystemIterator::SKIP_DOTS);
				$ri=new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
				foreach($ri as $file) {
					if($file->isDir()){
						rmdir($file);
					}else{
						unlink($file);
					}
				}
				rmdir($_SERVER['DOCUMENT_ROOT'].$path);
				PageLog::action($path, $GLOBALS['AUTH'][0], PageLog::DELETE);
			}else{
				PageLog::action($path, $GLOBALS['AUTH'][0], PageLog::DELETE, false);
				return new Result("Удаляемая папка не найдена");
			}
		}
		public static function clearPath($path, $stat=null, $name=null){
			if(($stat==null)or($name==null)){
				$stat=$GLOBALS['AUTH'][2];
				$name=$GLOBALS['AUTH'][0];
			}
			$path=str_replace("\\", "/", $path);
			$path=preg_replace("@\.php$@i", "", $path);
			$path=preg_replace("@[^a-zA-Z1-90_/]@", "", $path);
			$path=trim($path, "/");
			if(Stats::can($stat, "cmsroot")){
				while(strpos($path, "//")!==false){
					$path=str_replace("//", "", $path);
				}
				return("/".$path.".php");
			}
			$path=str_ireplace("cms", "", $path);
			$path=ltrim($path, "/");
			while(strpos($path, "//")!==false){
				$path=str_replace("//", "", $path);
			}
			if(Stats::can($stat, "root")){
				return("/".$path.".php");
			}
			if(strpos($path, $name."/")!==0){
				$path=$name."/".$path;
			}
			return("/".ltrim($path, "/").".php");
		}
		public static function strtodata($str){
			if($str==""){
				return array();
			}
			$str=explode('|', $str);
			$DATA=array();
			$tmpcount=count($str);
			for($i=0;$i<$tmpcount;++$i){
				if(!isset($str[$i+1])){
					break;
				}
				$DATA[$str[$i]]=$str[$i+1];
				++$i;
			}
			return $DATA;
		}
		public static function datatostr($data){
			if(!is_array($data)){
				return '';
			}
			$str="";
			foreach($data as $k->$v){
				$k=preg_replace("@[^a-zA-Z_]@", "", $k);
				$l=preg_replace("@[^a-zA-Z_а-яА-Яё1-90, \.\?]@u", "", $l);
				$str.="|".$k."|".$v;
			}
			return ltrim($str, '|');
		}
		#endregion
		#region display
		public static function Site($s, $USER, $DATA, $tohead=""){
			Page::ob();
			AntiXSS::H();
			Web::headerEncode();
			$s=Pattern::getReal($s);
			if($s!=null){
				if($USER!=''){
					if(!Pattern::canUs(Users::GetStat($USER), $s)){
						$s=Pattern::getDefault();
					}
					if($s==null){
						echo("<!doctype html><html><head></head><body>");
						Page::$foot="</body></html>";
						goto theend;
					}
				}
				if(!Pattern::exists($s)){
					echo("<!doctype html><html><head></head><body>");
					Page::$foot="</body></html>";
					goto theend;
				}else{
					$doc=Pattern::get($s);
					$doc=$doc['patt'];
					$doc=str_replace("<!--STYLE-->", "<link rel=\"stylesheet\" href=\"/cms/getscss.php?css=$s\" type=\"text/css\">", $doc);
					$doc=str_replace("<!--STYLES-->", "<link rel=\"stylesheet\" href=\"/cms/script.php?type=css\" type=\"text/css\">", $doc);
					$doc=str_replace("<!--SCRIPT-->", "<script src=\"/cms/script.php?type=js\"></script>", $doc);
					$doc=str_replace("<!--TOHEAD-->", $tohead, $doc);
					if(isset($DATA)){
						$DATA=Page::strtodata($DATA);
						foreach($DATA as $Dkey=>$Dvalue){
							$Dkey=preg_replace("@[^a-zA-Z]@", "", $Dkey);
							$Dvalue=preg_replace("@[^a-zA-Z_а-яА-Яё1-90]@u", "", $Dvalue);
							$doc=str_replace("<!--PAGE_$Dkey-->", $Dvalue, $doc);
						}
					}
					$_ar=explode("<!--TEXT-->", $doc);
					$_ar[0]=trim($_ar[0]);
					Page::$foot=smartHTML($_ar[1]);
					if(strpos($doc, "<!--PHP-->")!==false){
						$_SCRIPT_TYPE_FOR_INClude_in_GETSHABL___PHP="php";
						$doc=explode("<!--PHP-->", $_ar[0]);
						if($doc[0]!=null){
							echo(smartHTML($doc[0]));
						}
						include_once ($_SERVER['DOCUMENT_ROOT']."/cms/script.php");
						if(isset($doc[1])){
							echo(smartHTML($doc[1]));
						}
					}else{
						echo(smartHTML($_ar[0]));
					}
				}
			}else{
				echo("<!doctype html><html><head></head><body>");
				Page::$foot="</body></html>";
			}
			theend:;
			register_shutdown_function('\\LCMS\\Core\\Pages\\Page::footer');
		}
		public static function CMS($tohead=""){
			if(isset($_REQUEST['aj'])and($_REQUEST['aj']==1)){
				if(isset($_POST['aj'])){
					unset($_POST['aj']);
				}
				if(isset($_GET['aj'])){
					unset($_GET['aj']);
				}
				if(isset($_REQUEST['aj'])){
					unset($_REQUEST['aj']);
				}
				$_SERVER['argv'][0]=preg_replace('@aj\=1\&?@', '', $_SERVER['argv'][0]);
				if($tohead!=""){
					echo('<!--tohead-->');
				}
				CMSEnv::eco(filesize( __FILE__ )/4);
				if($GLOBALS['AUTH']!=false){
					include_once($_SERVER['DOCUMENT_ROOT']."/cms/exit.html");
					echo(static::$sd);
					return;
				}else{
					include_once($_SERVER['DOCUMENT_ROOT']."/cms/enter.html");
					exit;
				}
			}
			Page::ob();
			$s=Pattern::getReal(Pattern::getCMS());
			AntiXSS::H();
			Web::headerEncode();
			if($s!=null){
				if(!Pattern::exists($s)){
					echo("<!doctype html><html><head></head><body>");
					Page::$foot="</body></html>";
				}else{
					$doc=Pattern::get($s);
					$doc=$doc['patt'];
					$doc=str_replace("<!--STYLE-->", "<link rel=\"stylesheet\" href=\"/cms/getscss.php?css=$s\" type=\"text/css\">", $doc);
					$doc=str_replace("<!--STYLES-->", "<link rel=\"stylesheet\" href=\"/cms/script.php?type=css\" type=\"text/css\">", $doc);
					$doc=str_replace("<!--SCRIPT-->", "<script src=\"/cms/script.php?type=js\"></script>", $doc);
					$doc=str_replace("<!--TOHEAD-->", $tohead, $doc);
					$_ar=explode("<!--TEXT-->", $doc);
					$_ar[0]=trim($_ar[0]);
					Page::$foot=smartHTML($_ar[1]);
					if(strpos($doc, "<!--PHP-->")!==false){
						$_SCRIPT_TYPE_FOR_INClude_in_GETSHABL___PHP="php";
						$doc=explode("<!--PHP-->", $_ar[0]);
						if($doc[0]!=null){
							echo(smartHTML($doc[0]));
						}
						include_once ($_SERVER['DOCUMENT_ROOT']."/cms/script.php");
						if($doc[1]!=null){
							echo(smartHTML($doc[1]));
						}
					}else{
						echo(smartHTML($_ar[0]));
					}
				}
			}else{
				echo("<!doctype html><html><head></head><body>");
				Page::$foot="</body></html>";
			}
			if($GLOBALS['AUTH']!=false){
				include_once($_SERVER['DOCUMENT_ROOT']."/cms/exit.html");
				echo(static::$sd);
			}else{
				include_once($_SERVER['DOCUMENT_ROOT']."/cms/enter.html");
				Page::footer();
				exit;
			}
			echo('<script src="/cms/ajax.js"></script>');
		}
		public static function footer(){
			echo(Page::$foot);
			Page::deob();
			exit;
		}
		#endregion
		#region ob
		private static $o;
		private static function ob(){
			static::$o=CMSEnv::getEcoMode();
			if(static::$o){
				ob_start("\\LCMS\\Core\\Pages\\Page::hob");
			}
		}
		private static function deob(){
			if(static::$o){
				ob_end_flush();
			}
		}
		private static function zippage($str){
			$a=strlen($str);
			$newstr="";
			$prelast="";
			$last="";
			$d=true;
			$current="";
			for($i=0;$i<$a;++$i){
				$current=$str[$i];
				if($d){
					//textarea or pre
					if(($prelast=="<")and((($last=="t")and($current=="e"))or(($last=="p")and($current=="r")))){
						$d=false;
					}
					if($current=="\r"){
						$current=" ";
						goto ef;
					}
					if($current=="\n"){
						$current=" ";
						goto ef;
					}
					if($current=="\t"){
						$current=" ";
						goto ef;
					}
					if(($current==" ")and($last==" ")){
						$last="";
						goto ef;
					}
					if(($last==">")and($current==" ")){
						//$current=">";
						//$last="";
						goto ef;
					}
					if(($last=="<")and($current==" ")){
						$current="<";
						$last="";
						goto ef;
					}
					if(($last==" ")and($current==">")){
						$last="";
						goto ef;
					}
					if(($prelast==" ")and($last=="<")and($current=="/")){
						$prelast="";
						goto ef;
					}
					if(($prelast==" ")and($last=="/")and($current==">")){
						$prelast="";
						goto ef;
					}
				}else{
					if(($prelast=="<")and($last=="/")and($current=="p")){
						$d=true;
						goto ef;
					}
					if(($prelast=="<")and($last=="/")and($current=="t")){
						$d=true;
					}
				}
				ef:;
				$newstr.=$prelast;
				$prelast=$last;
				$last=$current;
			}
			return trim($newstr.$prelast.$last);
		}
		public static function hob($str){
			$a=strlen($str);
			$str=Page::zippage($str);
			$str=Page::zippage($str);
			if(function_exists('gzdeflate')){
				CMSEnv::eco($a-strlen(gzdeflate($str, 9)));
			}else{
				CMSEnv::eco($a-strlen($str));
			}
			return $str;
		}
		#endregion
		#region sudo
		private static $sd='';
		public static function sudo(){
			static::$sd='<form action="?" method="get"><input type="submit" value="Стать собой" name="i" /></form>';
		}
		#endregion
	}
	function smartHTML($html, $_DATA=null){
		$html=str_replace("<!--BGR_THEME-->", file_get_contents($_SERVER['DOCUMENT_ROOT']."/cms/bg/theme.part"), $html);
		$html=str_replace("<!--BGR_FONE-->", file_get_contents($_SERVER['DOCUMENT_ROOT']."/cms/bg/fone.part"), $html);
		$html=trim($html);
		if($_DATA!=null){
			foreach($_DATA as $k=>$l){
				$k=strtoupper($k);
				$html=str_replace("<!--PAGE_$k-->", $l, $html);
			}
		}
		$html=preg_replace_callback('@<!\-\-THE_([a-zA-Z1-90]+)\-\->@', "\\LCMS\\Core\\Pages\\preg_smartHTML_part", $html);
		$html=preg_replace_callback('@<!\\-\\-IsInstalled\\(((.*?)(?:, ?(.*?))?)\\)\\-\\->(.*?)<!\\-\\-/IsInstalled\\(\\1\\)\\-\\->@s', "\\LCMS\\Core\\Pages\\preg_smartHTML_yinstall", $html);
		$html=preg_replace_callback('@<!\\-\\-NotInstalled\\(((.*?)(?:, ?(.*?))?)\\)\\-\\->(.*?)<!\\-\\-/NotInstalled\\(\\1\\)\\-\\->@s', "\\LCMS\\Core\\Pages\\preg_smartHTML_ninstall", $html);
		$html=preg_replace_callback('@\-\-\-(.*?)\-\-\-@', "\\LCMS\\Core\\Actions\\locale", $html);
		return $html;
	}
	function preg_smartHTML_part($m){
		$part=strtolower($m[1]);
		$part=str_replace('.', '', $part);
		$part=$_SERVER['DOCUMENT_ROOT']."/cms/parts/".$part.".part";
		if(file_exists($part)){
			return(file_get_contents($part));
		}else{
			return("<div style=\"color: red; position: fixed; z-index: 9999999;\"><big><b>Несуществующий элемент: $part</b></big></div>");
		}
	}
	function preg_smartHTML_yinstall($m){
		return preg_smartHTML_install($m, true);
	}
	function preg_smartHTML_ninstall($m){
		return preg_smartHTML_install($m, false);
	}
	function preg_smartHTML_install($m, $val){
		$a=explode('|', $m[2]);
		$what=$a[0];
		switch(strtolower($what)){
			case 'script':
				$what=Modules\ScriptModules::Type;
				break;
			case 'part':
				$what=Part::Type;
				break;
			case 'pattern':
				$what=Pattern::Type;
				break;
			case 'plugin':
				$what=Modules\Plugins::Type;
				break;
		}
		switch(strtolower($m[3])){
			case 'php':
				$b=Modules\ScriptModules::PHP;
				break;
			case 'js':
				$b=Modules\ScriptModules::JS;
				break;
			case 'css':
				$b=Modules\ScriptModules::CSS;
				break;
			case 'php_css':
				$b=Modules\ScriptModules::PHP|Modules\ScriptModules::CSS;
				break;
			case 'php_js':
				$b=Modules\ScriptModules::PHP|Modules\ScriptModules::JS;
				break;
			case 'php_css_js':
				$b=Modules\ScriptModules::PHP|Modules\ScriptModules::CSS|Modules\ScriptModules::JS;
				break;
			case 'css_js':
				$b=Modules\ScriptModules::CSS|Modules\ScriptModules::JS;
				break;
			default:
				return '';
		}
		if(Modules\Modularity::isInstalled($what, $a[1], $b)==$val){
			return $m[4];
		}else{
			return '';
		}
	}
}
?>