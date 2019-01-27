<?php
namespace LCMS\Core{
	use \DOMNode;
	use \DOMDocument;
	class TagStripper extends ITagStripper{
		public function strip($html){
			$m=array();
			if($this->PHP){
				$html=str_replace("<?", "<code>&lt;?", $html);
				$html=str_replace("?>", "?&gt;</code>", $html);
				$html=preg_replace("@php@i", "<code>\\0</code>", $html);
				$html=str_replace("<%", "<code>&lt;%", $html);
				$html=str_replace("%>", "%&gt;</code>", $html);
			}else{
				$html=str_replace("<php>", "", $html);
				$html=str_replace("</php>", "", $html);
				preg_match("@\<\?.*?\?\>@s", $html, $m);
				$html=preg_replace("@\<\?.*?\?\>@s", '<php></php>', $html);
			}
			try{
				$dirty=new DOMDocument;
				$dirty->loadHTML('<?xml encoding="utf-8" ?>' .$html);//"<html><head></head><body>$html</body></html>"
				$dirtyBody=$dirty->getElementsByTagName('body')->item(0);
				$clean=new DOMDocument();
				$cleanBody=$clean->appendChild($clean->createElement('body'));
				$this->copyNodes($dirtyBody, $cleanBody);
				$stripped='';
				foreach($cleanBody->childNodes as $node){
					$stripped.=$clean->saveXml($node);
				}
			}
			catch(Exception $e){
				$stripped="INCORRECT_HTML:".htmlentities($html, ENT_QUOTES, 'utf-8');
			}
			if(!($this->PHP)){
				foreach($m as $line){
					$stripped=str_replace_once("&lt;php&gt;&lt;/php&gt;", $line, $stripped);
				}
			}
			return trim($stripped);
		}
		protected function copyNodes(DOMNode $dirty, DOMNode $clean){
			foreach($dirty->attributes as $name=>$valueNode){
				if(!isset($this->allowed[$dirty->nodeName])){
					break;
				}
				if(isset($this->allowed[$dirty->nodeName][$name])){
					$attr=$clean->ownerDocument->createAttribute($name);
					$attr->value=$valueNode->value;
					$clean->appendChild($attr);
				}
			}
			foreach($dirty->childNodes as $child){
				if(($child->nodeType==XML_ELEMENT_NODE)and(isset($this->allowed[$child->nodeName]))){
					$node=$clean->ownerDocument->createElement($child->nodeName);
					$clean->appendChild($node);
					$this->copyNodes($child, $node);
				}elseif($child->nodeType==XML_TEXT_NODE){
					$text=$clean->ownerDocument->createTextNode($child->textContent);
					$clean->appendChild($text);
				}else{
					$text=$clean->ownerDocument->createTextNode("<".$child->nodeName .">".$child->textContent . "</".$child->nodeName .">");
					$clean->appendChild($text);
				}
			}
		}
	}
}
?>