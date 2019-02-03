<?php
namespace LCMS\Core{
	class LXML extends IXML{
		public function getNodes($tag, $arg=array(), $i){
			if($i){
				$m="sui";
			}else{
				$m="su";
			}
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
		public function has($tag){return(preg_match("@<".preg_quote($tag, "@")." ?/>@u", $this->contents)!=0);}
		public function hasopen($tag){return(preg_match("@<".preg_quote($tag, "@").".*?>@u", $this->contents)!=0);}
		public function node($path, $i=false){
			$path=explode(".", $path);
			$current=$this;
			foreach($path as $el){
				$el=explode('*', $el);
				if(!isset($el[1])){
					$el[1]=-1;
				}
				$current=$current->getNode($el[0], str2arr($el[1]), $i);
				$current=$current[0];
			}
		}
		public function zip(){return($this->azip()->azip());}
		protected function azip(){
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
			return $this;
		}
	}
}
?>
