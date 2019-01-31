<?php
namespace LCMS\Core{
	class IText{
		const PARSE_NONE=0;
		const PARSE_LOCALE=1;
		const PARSE_ACTION=2;
		const PARSE_PART=4;
		const PARSE_SPC_PART=8;
		const PARSE_INSTALLED=16;
		const PARSE_PAGE=PARSE_PART|PARSE_SPC_PART|PARSE_INSTALLED;
		const PARSE_ALL=PARSE_LOCALE|PARSE_ACTION|PARSE_PAGE;
		abstract public function parse($content, $flags=static::PARSE_ALL, $options=array());
		abstract public function eol($text);
	}
}

