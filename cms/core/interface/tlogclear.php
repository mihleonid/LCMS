<?php
namespace LCMS\Core{
	class TLogClear{
		public static function clear($n){
			$log=Path::get(static::path());
			$log=explode("\n", $log);
			$n=min($n, count($log));
			$n=count($log)-$int;
			for($i=0;$i<$n;++$i){
				unset($log[$i]);
			}
			Path::put(static::path(), implode("\n", $log));
			return new Result();
		}
	}
}
?>