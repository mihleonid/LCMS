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
	class LXML extends ILXML{
		public function getNodes($tag, $arg=array()){
			$m="sui";
			$tag=preg_quote($tag, "@");
			if($arg==array()){
				$i=preg_match("@<$tag>(.*?)</$tag>@$m", $this->contents, $matches);
				if($i==0){
					return new static("", false);
				}
				return new static($matches[1], false);
			}
			if($arg==null){
				preg_match("@<$tag(.*?)>(.*?)</$tag>@$m", $this->contents, $matches);
				if($i==0){
					return new static("", false);
				}
				return new static($matches[1], false);
			}
			$s="";
			foreach($arg as $k=>$i){
				if($i===true){
					$s.=" $k";
				}else{
					$i=preg_quote($i, "@");
					$s.=" $k=\"$i\"";
				}
			}
			$i=preg_match("@<$tag".$s.">(.*?)</$tag>@$m", $this->contents, $matches);
			if($i==0){
					return new static("", false);
			}
			return array(new static($matches[1], false));
		}
		public function has($tag){
			$tag=preg_quote($tag, "@");
			return(preg_match("@<$tag ?/>@u", $this->contents)!=0);
		}
		public function hasopen($tag){
			$tag=preg_quote($tag, "@");
			return(preg_match("@<$tag.*?>@u", $this->contents)!=0);
		}
		public function node($path){
			$path=explode(".", $path);
			$current=$this;
			foreach($path as $el){
				$el=explode('*', $el);
				if(!isset($el[1])){
					$el[1]=-1;
				}
				$current=$current->getNode($el[0], str2arr($el[1]));
				$current=$current[0];
			}
		}
		public function zip(){
			$this->azip();
			$this->azip();
			return $this;
		}
		private function azip(){
			$a=strlen($this->contents);
			$newstr="";
			$prelast="";
			$last="";
			$d=true;
			$current="";
			for($i=0;$i<$a;++$i){
				$current=$str[$i];
				if($d){
					//textarea or pre
					if(($prelast=="<")and((($last=="t")and($current=="e"))or(($last=="p")and($current=="r")))){
						$d=false;
					}
					if($current=="\r"){
						$current=" ";
						goto ef;
					}
					if($current=="\n"){
						$current=" ";
						goto ef;
					}
					if($current=="\t"){
						$current=" ";
						goto ef;
					}
					if(($current==" ")and($last==" ")){
						$last="";
						goto ef;
					}
					if(($last==">")and($current==" ")){
						//$current=">";
						//$last="";
						goto ef;
					}
					if(($last=="<")and($current==" ")){
						$current="<";
						$last="";
						goto ef;
					}
					if(($last==" ")and($current==">")){
						$last="";
						goto ef;
					}
					if(($prelast==" ")and($last=="<")and($current=="/")){
						$prelast="";
						goto ef;
					}
					if(($prelast==" ")and($last=="/")and($current==">")){
						$prelast="";
						goto ef;
					}
				}else{
					if(($prelast=="<")and($last=="/")and($current=="p")){
						$d=true;
						goto ef;
					}
					if(($prelast=="<")and($last=="/")and($current=="t")){
						$d=true;
					}
				}
				ef:;
				$newstr.=$prelast;
				$prelast=$last;
				$last=$current;
			}
			$this->contents=trim($newstr.$prelast.$last);
		}
	}
}
?>