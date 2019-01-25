<?php
namespace LCMS\Core{
	function call($cl, $method, $args=array()){
		if(!is_array($args)){
			if(is_string($args)){
				$args=explode(",", $args);
				$c=count($args);
				for($i=0;$i<$c;++$i){
					$args[$i]=trim($args[$i]);
				}
			}else{
				$args=array();
			}
		}
		$method=trim($method);
		$cl=Moduler::getClass($cl);
		#todo
		if(!function_exists($cl."::".$method)){
			return Pool::CRASH;
		}
		return call_user_func_array($cl."::".$method, $args);
	}
}
?>