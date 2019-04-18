<?php
namespace LCMS\Core{
	class Counter{
		public static function setA($deactive){
			return Loc::set("counter", $deactive);
		}
		public static function getA(){
			return Loc::get("counter", true);
		}
		public static function resetA(){
			return Counter::setA(!Counter::getA());
		}
		public static function clear(){
			return Loc::set("uncodeform", 1);
		}
		public static function get($check=false){
			if($check!==false){
				if(Counter::getA()){
					return true;
				}
				$int=Loc::get("uncodeform");
				$int=Counter::plus($int);
				if($check==$int){
					Loc::set("uncodeform", Counter::plus($int));
					return true;
				}else{
					return false;
				}
			}else{
				$int=Loc::get("uncodeform");
				return Counter::plus($int);
			}
		}
		private static function maxInt(){
			$int=intval((PHP_INT_MAX-1000)/20-15);
			$mod=($int%2);
			return($int+$mod+1);
		}
		private static function plus($int){
			$int=(($int+1) % ( Counter::maxInt() ));
			if($int==0){
				$int=2;
			}
			return $int;
		}
	}
}

