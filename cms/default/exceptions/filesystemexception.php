<?php
namespace LCMS\Core{
	class FileSystemException extends \Exception{
		public function __construct($message, $code=0, $previous=null){
			Pool::trouble();
			parent::__construct($message, $code, $previous);
		}
	}
}
