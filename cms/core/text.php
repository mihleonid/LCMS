<?php
namespace LCMS\Core{
	class Text{
		const PARSE_NONE=0;
		const PARSE_LOCALE=1;
		const PARSE_ACTION=2;
		const PARSE_PART=4;
		const PARSE_SPC_PART=8;
		const PARSE_PAGE=PARSE_PART|PARSE_SPC_PART;
		const PARSE_ALL=PARSE_LOCALE|PARSE_ACTION|PARSE_PAGE;
		public static function parse($content, $flags=PARSE_ALL, $options=array()){
			if(bitmask($flags, static::PARSE_SPC_PART)){
				$pattern="<default>";
				if(isset($options['pattern'])){
					
				}
				$doc=str_replace("<!--STYLE-->", "<link rel=\"stylesheet\" href=\"/".Path::cms("getcss.php")."?css=".$pattern."\" type=\"text/css\">", $doc);
				$doc=str_replace("<!--STYLES-->", "<link rel=\"stylesheet\" href=\"/".Path::cms("script.php")."?type=css\" type=\"text/css\">", $doc);
				$doc=str_replace("<!--SCRIPT-->", "<script src=\"/".Path::cms("script.php")."?type=js\"></script>", $doc);
			}
		}
		public static function eol($text){
			$text=trim($text);
			$text=str_replace("\r\n", "\n", $text);
			$text=str_replace("\r", "\n", $text);
			$text=str_replace("\n", "\r\n", $text);
			return $text;
		}
	}
}
?>