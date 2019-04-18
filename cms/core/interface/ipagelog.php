<?php
namespace LCMS\Core{
	abstract class IPageLog extends TLogClear{
		const ADD=2;
		const EDIT=3;
		const DELETE=4;
		public static function path(){
			return Path::cms("page.log");
		}
		abstract public function put($path, $user, $type, $ok=true);
		public static function logging(){
			return true;
		}
	}
}
