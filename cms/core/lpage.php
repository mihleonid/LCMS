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
			$s=Pattern::getReal($s);
			if($s!=null){
				if($USER!=''){
					if(User::exists($USER)){
						if(!Pattern::canUs(User::status($USER), $s)){
							$s=Pattern::getDefault();
						}
					}
					if($s==null){
						echo("<!doctype html><html><head></head><body>");
						static::$foot="</body></html>";
						return null;
					}
				}
				$doc=Pattern::get($s);
				$doc=$doc['patt'];
				$doc=Text::parse($doc, Text::PARSE_ALL, array('data'=>$DATA, 'pattern'=>$s, 'tohead'=>$tohead));
				$ar=explode("<!--!TEXT-->", $doc);
				$ar[0]=trim($ar[0]);
				static::$foot=trim($ar[1]);
				if(strpos($doc, "<!--!PHP-->")!==false){
					$doc=explode("<!--!PHP-->", $ar[0]);
					if($doc[0]!=null){
						echo(trim($doc[0]));
					}
					Handler::page();
					if(isset($doc[1])){
						echo(trim($doc[1]));
					}
				}else{
					echo(trim($ar[0]));
				}
			}else{
				echo("<!doctype html><html><head></head><body>");
				static::$foot="</body></html>";
			}
		}
		protected static function enter(){
			Path::sinclude(Path::cms("enter.html"));
			static::footer();
			exit;
		}
		protected static function quit(){
			Path::sinclude(Path::cms("exit.html"));
			echo(static::$sd);
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
				$_SERVER['argv'][0]=preg_replace('@\\??\\&?aj\\=1@', '', $_SERVER['argv'][0]);
				if($tohead!=""){
					echo('<!--tohead-->');
				}
				if(User::authHas()){
					static::uit();
					return;
				}else{
					static::enter();
					exit;
				}
			}
			$s=Pattern::getCMS();
			if($s!=null){
				$doc=Pattern::get($s);
				$doc=$doc['patt'];
				$doc=Text::parse($doc, Text::PARSE_ALL, array('pattern'=>$s, 'tohead'=>$tohead));
				$ar=explode("<!--!TEXT-->", $doc);
				$ar[0]=trim($ar[0]);
				static::$foot=trim($ar[1]);
				if(strpos($ar[0], "<!--!PHP-->")!==false){
					$doc=explode("<!--!PHP-->", $ar[0]);
					if($doc[0]!=""){
						echo($doc[0]);
					}
					Handler::page();
					if(isset($doc[1])){
						echo($doc[1]);
					}
				}else{
					echo($ar[0]);
				}
			}else{
				echo("<!doctype html><html><head></head><body>");
				static::$foot="</body></html>";
			}
			if(User::authHas()){
				static::quit();
			}else{
				static::enter();
			}
			echo('<script src="/cms/ajax.js"></script>');
		}
		public static function footer(){
			if(static::$footer==""){
				return null;
			}
			if(strpos(static:$foot, "<!--!PHP-->")!==false){
				$doc=explode("<!--!PHP-->", static:$foot);
				if($doc[0]!=""){
					echo($doc[0]);
				}
				Handler::page();
				if(isset($doc[1])){
					echo($doc[1]);
				}
			}else{
				echo(static:$foot);
			}
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
}
?>
