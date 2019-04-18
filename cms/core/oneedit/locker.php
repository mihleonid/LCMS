<?php
namespace LCMS\Core{
	class Locker{
		public static function setA($deactive){
			return Loc::set("locker", $deactive);
		}
		public static function getA(){
			return Loc::get("locker", true);
		}
		public static function resetA(){
			return Locker::setA(!Locker::getA());
		}
		public static function set(){
			if(Locker::getA()){
				return true;
			}
			if(!Loc::exists("lock")){
				Loc::set("lock", time());
				return true;
			}
			$con=Loc::get("lock", null);
			if(($con==null)or(($con+120)<time())){
				Loc::set("lock", time());
				return true;
			}
			return false;
		}
		public static function unlock(){
			return Loc::set("lock", null);
		}
	}
}

