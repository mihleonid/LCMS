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
				if(isset($options['tohead'])){
					$doc=str_replace("<!--!TOHEAD-->", $options['tohead'], $doc);
				}
			}
			if(bitmask($flags, static::PARSE_INSTALLED)){
				$html=preg_replace_callback('@<!\\-\\-IsInstalled\\(((.*?)(?:, ?(.*?))?)\\)\\-\\->(.*?)<!\\-\\-/IsInstalled\\(\\1\\)\\-\\->@s', "\\LCMS\\Core\\Text::preg_smartHTML_yinstall", $html);
				$html=preg_replace_callback('@<!\\-\\-NotInstalled\\(((.*?)(?:, ?(.*?))?)\\)\\-\\->(.*?)<!\\-\\-/NotInstalled\\(\\1\\)\\-\\->@s', "\\LCMS\\Core\\Pages\\preg_smartHTML_ninstall", $html);
			}
		}
		public static function eol($text){
			$text=trim($text);
			$text=str_replace("\r\n", "\n", $text);
			$text=str_replace("\r", "\n", $text);
			$text=str_replace("\n", "\r\n", $text);
			return $text;
		}
		static function preg_smartHTML_part($m){
			$part=strtolower($m[1]);
			$part=str_replace('.', '', $part);
			$part=$_SERVER['DOCUMENT_ROOT']."/cms/parts/".$part.".part";
			if(file_exists($part)){
				return(file_get_contents($part));
			}else{
				return("<div style=\"color: red; position: fixed; z-index: 9999999;\"><big><b>Несуществующий элемент: $part</b></big></div>");
			}
		}
		static function preg_smartHTML_yinstall($m){
			return preg_smartHTML_install($m, true);
		}
		static function preg_smartHTML_ninstall($m){
			return preg_smartHTML_install($m, false);
		}
		static function preg_smartHTML_install($m, $val){
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

