<?php
namespace LCMS\Core{
	class DBase{
		protected $path=null;
		protected $arr=null;
		protected $module=null;
		protected $name=null;
		public function __construct($module, $name=null){
			$module=strip($module);
			if($name!==null){
				$name=strip($name);
			}
			if($name==null){
				if($module==""){
					$module="mane";
				}
				$this->path=Data::path(null, $name, "db");
				$this->name=$module;
				$this->module="storage";
			}else{
				if(Moduler::exists($module)){
					$this->path=Data::path($module, $name, "db");
					$this->module=$module;
					$this->name=$name;
				}else{
					throw new DBaseException("Path is icorrect");
				}
			}
		}
		protected function copen(){
			if(file_exists($this->path)){
				$this->arr=uncode(file_get_contents($this->path));
				if($this->arr==Pool::CRASH){
					$this->arr=array();
				}
			}else{
				$this->arr=array();
			}
			Mutex::set($this->module, $this->name);
		}
		public function open(){
			if($this->arr==null){
				$this->copen();
			}
		}
		public function exists($key){
			$this->open();
			return isset($this->arr[$key]);
		}
		public function get($key=null){
			$this->open();
			if($key==null){
				return $this->arr;
			}else{
				if($this->exists($key)){
					return $this->$key;
				}else{
					throw new DBaseException("Not exists");
				}
			}
		}
		public function counted(){
			$this->open();
			return count($this->arr);
		}
		public function set($key, $val){
			$this->open();
			$this->arr[$key]=$val;
			return $this;
		}
		public function setAll($arr){
			$this->arr=$arr;
			return $this;
		}
		public function clear(){
			$this->open();
			$this->arr=array();
			return $this;
		}
		public function delete($key){
			$this->open();
			if($this->exists($key)){
				unset($this->arr[$key]);
			}
			return $this;
		}
		public function write(){
			$this->open();
			file_put_contents($this->path, code($this->arr));
		}
		public function close(){
			$this->arr=null;
			Mutex::delete($this->module, $this->name);
		}
	}
}
?>