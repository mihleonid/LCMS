<?php
namespace LCMS\Core{
	abstract public class IStatus extends GlobalRW{
		abstract protected function path(){
			return Path::cms("status.db");
	}
}

