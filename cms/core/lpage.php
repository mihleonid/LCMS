<?php
namespace LCMS\Core{
	class LPage extends IPage{
		private static $foot;
		public static function delete($path){
			$path=static::path($path);
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
			$path=static::path($path);
			$res=Path::rm($path);
			if($res){
				return true;
				PageLog::put($path, User::authName(), PageLog::DELETE);
			}else{
				PageLog::put($path, User::authName(), PageLog::DELETE, false);
				return $new Result("---pagenotexists---");
			}
		}
		public static function clearPath($path, $stat=null, $name=null){
			if(($stat==null)or($name==null)){
				$stat=User::authStatus();
				$name=User::authName();
			}
			$path=str_replace(".php", "", $path);
			$path=Path::iabs($path);
			$clear="";
			if(Status::can($stat, "cmsroot")){
				$clear=Path::concat(Path::cmssite($path), ".php");
			}else{
				if(Status::can($stat, "root")){
					$clear=Path::site($path);
				}else{
					$clear=Path::concat("blog/".$name, Path::site($path));
				}
			}
			return $clear;
		}
		public static function Site($s, $USER, $DATA, $tohead=""){
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
			static::sudo();
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
				$_SERVER['argv'][0]=preg_replace('@\??aj\=1\&?@', '', $_SERVER['argv'][0]);
				if($tohead!=""){
					echo('<!--tohead-->');
				}
				if(User::authHas()){
					include_once($_SERVER['DOCUMENT_ROOT']."/cms/exit.html");
					echo(static::$sd);
					return;
				}else{
					include_once($_SERVER['DOCUMENT_ROOT']."/cms/enter.html");
					exit;
				}
			}
			$s=Pattern::getCMS();
			if($s!=null){
				$doc=Pattern::get($s);
				$doc=$doc['patt'];
				$doc=Text::parse($doc, Text::PARSE_ALL, array('pattern'=>$s, 'tohead'=>$tohead));
				$_ar=explode("<!--TEXT-->", $doc);
				$_ar[0]=trim($_ar[0]);
				static::$foot=smartHTML($_ar[1]);
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
			}else{
				echo("<!doctype html><html><head></head><body>");
				static::$foot="</body></html>";
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
			echo(static::$foot);
		}
		private static $sd="";
		public static function sudo(){
			if(isset($_GET['sudo'])){
				User::sudo($_GET['sudo']);
				static::$sd='<form action="?" method="get"><input type="submit" value="Стать собой" name="i" /></form>';
			}else{
				if(isset($_GET['i'])){
					User::unsudo();
				}
			}
		}
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
		$html=preg_replace_callback('@\-\-\-(.*?)\-\-\-@', "\\LCMS\\Core\\Actions\\locale", $html);
		return $html;
	}
}
?>
