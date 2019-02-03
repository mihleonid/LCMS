<?php
namespace LCMS\Core{
	class LText extends IText{
		public static function parse($content, $flags=static::PARSE_ALL, $options=array()){
			if(bitmask($flags, static::PARSE_SPC_PART)){
				$pattern="<default>";
				if(isset($options['pattern'])){
					$pattern=Pattern::getReal($options['pattern']);
				}
				$doc=str_replace("<!--!STYLE-->", "<link rel=\"stylesheet\" href=\"/".Path::cms("getcss.php")."?css=".$pattern."\" type=\"text/css\">", $doc);
				$doc=str_replace("<!--!STYLES-->", "<link rel=\"stylesheet\" href=\"/".Path::cms("script.php")."?type=css\" type=\"text/css\">", $doc);
				$doc=str_replace("<!--!SCRIPT-->", "<script src=\"/".Path::cms("script.php")."?type=js\"></script>", $doc);
				if(isset($options['data'])and(is_array($options['data'])){
					foreach($options['data'] as $k=>$l){
						$k=strtoupper($k);
						$html=str_replace("<!--PAGE_$k-->", $l, $html);
					}
				}
				if(isset($options['tohead'])){
					$doc=str_replace("<!--!TOHEAD-->", $options['tohead'], $doc);
				}
			}
			if(bitmask($flags, static::PARSE_PLUGIN)){
				$html=Handler::parse($html);
			}
			if(bitmask($flags, static::PARSE_INSTALLED)){
				$html=preg_replace_callback('@<!\\-\\-!IsInstalled\\(((.*?)(?:, ?(.*?))?)\\)\\-\\->(.*?)<!\\-\\-/IsInstalled\\(\\1\\)\\-\\->@s', "\\LCMS\\Core\\Text::pyinstall", $html);
				$html=preg_replace_callback('@<!\\-\\-!NotInstalled\\(((.*?)(?:, ?(.*?))?)\\)\\-\\->(.*?)<!\\-\\-/NotInstalled\\(\\1\\)\\-\\->@s', "\\LCMS\\Core\\Text::pninstall", $html);
			}
			if(bitmask($flags, static::PARSE_PART)){
				$html=preg_replace_callback('@<!\\-\\-!PART_(.*?)\\-\\->@', "\\LCMS\\Core\\Text::ppart", $html);
				$html=preg_replace_callback('@<!\\-\\-!THE_(.*?)\\-\\->@', "\\LCMS\\Core\\Text::ppart", $html);
			}
			if(bitmask($flags, static::PARSE_ACTION)){
				$html=preg_replace_callback('@<!\\-\\-!ACTION_(.*?)\\-\\->@', "\\LCMS\\Core\\Text::pact", $html);
			}
			if(bitmask($flags, static::PARSE_LOCALE)){
				$html=preg_replace_callback('@\\-\\-\\-(.*?)\\-\\-\\-@', "\\LCMS\\Core\\Text::plocale", $html);
				$html=preg_replace_callback('@\\+\\+\\+(.*?)\\+\\+\\+@', "\\LCMS\\Core\\Text::plocalew", $html);
			}
			if(bitmask($flags, static::PARSE_BGR)){
				$html=str_replace("<!--BGR_THEME-->", Path::get(Path::cms("bg/theme.htm")), $html);
				$html=str_replace("<!--BGR_FONE-->", Path::get(Path::cms("bg/fone.htm")), $html);
			}
			if(bitmask($flags, static::PARSE_SPC_ACT)){
				if(isset($options['a'])){
					$a=$optins['a'];
				}else{
					$a=Pool::CRASH;
				}
				$html=str_ireplace('|F|', '|Form||Header|', $html);
				$html=str_ireplace('|SHeader|', '<'.'?php echo(Form::Sheader()); ?'.'>', $html);
				$html=str_ireplace('|Header|', '<'.'?php echo(Form::Sheader()); ?'.'>|FH|', $html);
				$html=str_ireplace('|Form|', '<form action="/cms/action.php" method="POST">', $html);
				$html=str_ireplace('|FH|', '<input type="hidden" name="tsel" value=$ACTNAME$><input type="hidden" name="page" value=$PAGE$>', $html);
				$html=str_ireplace('$ACTNAME$', '"'.addslashes($a).'"', $html);
				$html=str_ireplace('|ACTNAME$', addslashes($a), $html);
				$html=str_ireplace('|ACTNAME|', $a, $html);
				$html=str_ireplace('$PAGE$', '"'.addslashes($_SERVER['PHP_SELF']).'"', $html);
				$html=str_ireplace('|PAGE$', addslashes($_SERVER['PHP_SELF']), $html);
				$html=str_ireplace('|PAGE|', $_SERVER['PHP_SELF'], $html);
			}
			return $html;
		}
		public static function eol($text){
			$text=trim($text);
			$text=str_replace("\r\n", "\n", $text);
			$text=str_replace("\r", "\n", $text);
			$text=str_replace("\n", "\r\n", $text);
			return $text;
		}
		private static function ppart($m){
			$part=strtolower($m[1]);
			if(Part::exists($part)){
				return(Part::get($part));
			}else{
				return("<div style=\"color: red; position: fixed; z-index: 9999999;\"><big><b>---nopart---: $part</b></big></div>");
			}
		}
		private static function plocale($m){
			return(ll($m[1]));
		}
		private static function plocalew($m){
			return("'".str_replace(array('\\', '\''), array('\\\\', '\\\''), static::locale($m))."'");
		}
		private static function pyinstall($m){
			return static::pinstall($m, true);
		}
		private static function pninstall($m){
			return static::pinstall($m, false);
		}
		private static function pinstall($m, $val){
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
}

