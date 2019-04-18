<?php
namespace LCMS\Core{
	class Code{
		const STYLE_MONOSPACE=1;
		public static function deleteCommentLine($line, $comment="//"){
			$pos=strpos($line, $comment);
			if($pos!==false){
				return substr($line, 0, $pos);
			}
			return $line;
		}
		public static function getCommentLine($line, $comment="//"){
			$pos=strpos($line, $comment);
			if($pos!==false){
				return substr($line, $pos);
			}
			return $comment;
		}
		public static function commentLine($line, $comment="//"){
			//todo optional
			$a=deleteCommentLine($line, $comment);
			$b=getCommentLine($line, $comment);
			return array($a, $b);
		}
		public static function eol($text){
			Text::eol();
			$text=preg_replace("@\r\n *\r\n@sui", "\r\n", $text);
			return $text;
		}
		public static function baseStyle($content, $options=0){
			$content=str_replace("\r", "\n", $content);
			$content=explode("\n", $content);
			$lines=array();
			foreach($content as $line){
				$line=trim($line);
				if(bitmask($options, static::STYLE_MONOSPACE)){
					$line=preg_replace("@ +@", " ", $line);
				}
				if($line!=""){
					$lines[]=$line;
				}
			}
			return implode("\r\n", $lines);
		}
	}
}

