<?php
namespace LCMS\Core{
	class SafetyException extends \Exception{
		public function __construct($message, $code=0, $previous=null){
			Pool::trouble();
			parent::__construct($message, $code, $previous);
		}
	}
}
?>