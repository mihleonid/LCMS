<?php
namespace LCMS\Core{
	abstract class LPattern extends IPattern{
		public static function get($s){
			return "<!--!TEXT-->";
		}
		public static function getCMS(){
			return null;
		}
		public static function getReal($pattern){
			return null;
		}
		public static function canUs($status, $pattern){
			return true;
		}
	}
}
?>
