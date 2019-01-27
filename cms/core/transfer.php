<?php
namespace LCMS\Core{
	class Transfer{
		public static function download($file, $data=array()){
			$content=IO::get($file, $data);
			header('Content-Description: CMS File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Expires: Sat, 26 Jul 1997 15:00:00 GMT');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.strlen($content));
			echo($content);
		}
		public static function upload(){
			if(!isset($_POST['docnums'])){// num of docs
				$nums=0;
			}else{
				$nums=intval($_POST['docnums']);
			}
			if($nums==0){
				return new Result('---nothingtoupload---');
			}
			$error=new Result();
			$names=array();
			for($i=0;$i<$nums;++$i){
				if(!isset($_FILES['doc'.$i])){
					continue;
				}
				if(!isset($_FILES['doc'.$i]['error'])){
					continue;
				}
				if(!isset($_FILES['doc'.$i]['name'])){
					continue;
				}
				if(!isset($_FILES['doc'.$i]['tmp_name'])){
					continue;
				}
				if(is_array($_FILES['doc'.$i]['error'])){
					foreach($_FILES['doc'.$i]['error'] as $key=>$e){
						if($e==0){
							if(!isset($_FILES['doc'.$i]['name'][$key])){
								continue;
							}
							if(!isset($_FILES['doc'.$i]['tmp_name'][$key])){
								continue;
							}
							$f=explode(".", $_FILES['doc']['name'][$key]);
							$name=strip(substr($f[0], 0, 32));
							if(isset($f[1])){
								$name.='.'.strip(substr($f[1], 0, 16));
							}
							$tpath=Path::tmpfile();
							$names[$tpath]=$name;
							if(Path::mov_up($_FILES['doc'.$i]['tmp_name'][$key], $tpath)){
								$error->add("---notmoved---");
							}
						}else{
							$error->add("---uploaderror---");
						}
					}
				}else{
					if($_FILES['doc'.$i]['error']==0){
						$f=explode(".", $_FILES['doc']['name']);
						$name=strip(substr($f[0], 0, 32));
						if(isset($f[1])){
							$name.='.'.strip(substr($f[1], 0, 16));
						}
						$tpath=Path::tmpfile();
						$names[$tpath]=$name;
						if(Path::mov_up($_FILES['doc'.$i]['tmp_name'], $tpath)){
							$error->add("---notmoved---");
						}
					}else{
						$error->add("---uploaderror---");
					}
				}
			}
			return $names;
		}
	}
}
?>