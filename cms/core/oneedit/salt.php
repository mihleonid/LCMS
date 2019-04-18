<?php
namespace LCMS\Core{
	class Salt{
		public static function setA($deactive){
			return Loc::set("salt", $deactive);
		}
		public static function getA(){
			return Loc::get("salt", true);
		}
		public static function resetA(){
			return Salt::setA(!Salt::getA());
		}
		public static function get(){
			if(Loc::get("z", null)==null){
				Loc::set("z", "%COMPIELER:RANDOM%");
			}
			return Loc::get("z");
		}
		public static function change($s){
			static::set($s);
		}
		public static function set($stepen){
			$step=intval($stepen);
			$step=min($step, 1000);
			$step=max($step, 1);
			Loc::set("step", $step);
			$str="";
			for($i=0;$i<$step;$i++){
				$str.=md5(mt_rand());
			}
			return Loc::set("z", $str);
		}
		public static function compare($a){
			if(Salt::getA()){
				return true;
			}
			return(trim($a)==trim(Salt::get()));
		}
	}
}

