<?php
namespace LCMS\MM\Users{
	use \LCMS\Core;
	use \LCMS\Core\Data;
	use \LCMS\Core\Result;
	use \LCMS\MM\Security\Hash;
	use \LCMS\Core\DBase;
	use \LCMS\MM\Logger\Log;
	class Clever{
		public static function setClever($name, $clever){
			if($name==null){
				$name=User::authName();
			}
			$r=null;
			if(Core\toBool($clever)){
				$r=static::addClever($name);
			}else{
				$r=static::deleteClever($name);
			}
			return $r;
		}
		public static function addClever($name=null){
			if($name==null){
				$name=User::authName();
			}
			if(User::exists($name)){
				$a=Data::get("users", "clever");
				$a[$name]=true;
				return Data::set("users", "clever", $a);
			}else{
				return new Result("---users.usernotexists---");
			}
		}
		public static function deleteClever($name=null){
			if($name==null){
				$name=User::authName();
			}
			$a=Data::get("users", "clever");
			if(isset($a[$name])){
				unset($a[$name]);
				return Data::set("users", "clever", $a);
			}
			return new Result("Пользователь уже не продвинутый");
		}
		public static function replaceClever($name=null){
			if($name==null){
				$name=User::authName();
			}
			if(User::exists($name)){
				$a=Data::get("users", "clever");
				if(isset($a[$name])){
					unset($a[$name]);
				}else{
					$a[$name]=true;
				}
				return Data::set("users", "clever", $a);
			}else{
				return new Result("---users.usernotexists---");
			}
		}
		public static function isClever($name=null){
			if($name==null){
				$name=User::authName();
			}
			$a=Data::get("users", "clever");
			if(isset($a[$name])){
				return true;
			}
			return false;
		}
	}
	class User{
		private static $base=null;
		private static $Login=null;
		public static function add($login, $password, $status, $realname){
			$login=Core\strip($login);
			$realname=Core\strip_ru($realname);
			if(static::$base ==null){
				static::$base = new DBase("users", "all");
			}
			if(static::$base->exists($login)){
				return new Result("---users.useralreadyexists---");
			}
			if(Status::exists($status)){
				(static::$base)->set($login, array(Hash::make($password), $status, $realname))->write();
				return new Result();
			}else{
				return new Result("---users.statusnotexists---");
			}
		}
		public static function changePassword($name, $pass){
			$tecus=static::authName();
			if(static::$base ==null){
				static::$base=new DBase("users", "all");
			}
			if(static::exists($name)){
				$line=(static::$base)->get($name);
				$pass=trim($pass);
				$line[0]=Hash::make($pass);
				static::$base ->set($name, $line)->write();
				if(trim($tecus)==trim($name)){
					static::enterIn($name);
					return new Result("--mypass---".$pass."</code></span>");
				}
				return new Result();
			}else{
				return new Result("---users.usernotexists---");
			}
		}
		public static function exists($name){
			if(static::$base ==null){
				static::$base=new DBase("users", "all");
			}
			return((static::$base)->exists(trim($name)));
		}
		public static function enterIn($login, $sudo=false){
			$section="admin";
			if($sudo){
				$section="sudo";
			}
			$arr=Data::get("users", "loggedin");
			if(static::exists($login)){
				gen:;
				$ol=Core\rnd();
				foreach($arr as $key=>$val){
					if($val[1]+Data::get("users", "timelogged", 0)<time()){#todo set
						unset($arr[$key]);
					}
					if($val==$ol){
						goto gen;
					}
				}
				$arr[$login]=$ol;
				setcookie($section, $login."-".$ol);
				return new Result();	
			}else{
				return new Result("---users.usernotexists---");
			}
		}
		public static function login($login, $pass){
			if(static::$base ==null){
				static::$base=new DBase("users", "all");
			}
			if(static::exists($login)){
				$user= static::$UserBase ->get_line($login);
				if(Hash::verify($pass, $user[0])){
					return enterIn($login);
				}
				return new Result("---users.passwordicorrect---");
			}else{
				return new Result("---users.usernotexists---");
			}
		}
		public static function authName(){
			$a=static::auth();
			return($a[0]);
		}
		public static function authStatus(){
			$a=static::auth();
			return($a[2]);
		}
		public static function authRealName(){
			$a=static::auth();
			return($a[3]);
		}
		public static function delete($login){
			if(static::$base ==null){
				static::$base=new DBase("users", "all");
			}
			if(!static::exists($login)){
				return new Result("---users.usernotexists---");
			}
			$tmpl=((static::$base)->get($login));
			if($tmpl[1]=="globaladmin"){
				$c=0;
				$arr=static::$base->get();
				foreach($arr as $line){
					if($line[1]=="globaladmin"){
						$c++;
					}
				}
			}else{
				$c=2;
			}
			if($c<=1){
				return new Result("---users.lastgloabal---");
			}else{
				static::deleteClever($login);
				static::$base->delete($login)->write();
			}
			return new Result();
		}
		public static function levelDown($name=null){
			if($name==null){
				$name=static::authName();
			}
			$name=trim($name);
			if(static::$base ==null){
				static::$base=new DBase("users", "all");
			}
			if(!static::exists($name)){
				return new Result("---users.usernotexists---");
			}
			$tmpl=((static::$base)->get(trim($name)));
			if($tmpl[1]=="globaladmin"){
				$c=0;
				$all=static::$base->get();
				foreach($all as $line){
					if($line[1]=="globaladmin"){
						$c++;
					}
				}
			}else{
				$c=2;
			}
			if($c<=1){
				return new Result("---users.lastglobal---");
			}else{
				$line=$tmpl;
				$lineold=$line;
				if(Status::$can ==null){
					Status::$can=new Dbase("users", "can");
				}
				$all=Status::$can->get();
				$lastkey=null;
				$key=null;
				foreach($all as $key=>$value){
					if($lastkey==$line[1]){
						$line[1]=$key;
						break;
					}
					$lastkey=$key;
				}
				if($lastkey==$line[1]){
					$line[1]=$key;
				}
				if($line==$lineold){
					return new Result("---users.impossibledownstatmin---");
				}
				(static::$base)->set($name, $line)->write();
			}
			return new Result();
		}
		public static function levelUp($name){
			if(static::$base ==null){
				static::$bsse=new DBase("users", "all");
			}
			$name= trim($name);
			if(! static::exists($name)){
				return new Result("---users.usernotexists---");
			}
			$line= static::$base ->get($name);
			if($line[1]=="globaladmin"){
				return new Result("---users.impossibledownstatmax---");
			}
			if(Status::$can ==null){
				Status::$can=new DBase("users", "can");
			}
			$all=Status::$can->get();
			$lastkey=null;
			foreach($all as $key=>$value){
				if($key==$line[1]){
					$line[1]=$lastkey;
				}
				$lastkey=$key;
			}
			(static::$base)->set($name, $line)->write();
			return new Result();
		}
		#endregion
		public static function auth(){
			if(static::$Login==null){
				static::$Login=static::LogInCheck();
			}
			return static::$Login;
		}
		private static function LogInCheck(){
			if(!isset($_COOKIE)){
				return false;
			}
			if(!isset($_COOKIE["admin"])){
				return false;
			}
			if(!preg_match("@.+-[a-zA-Z1-90]@u", $_COOKIE['admin'])){
				return false;
			}
			if(static::$base ==null){
				static::$base=new DBase("users", "all");
			}
			$auf=explode("-", $_COOKIE['admin']);
			$login=trim($auf[0]);
			$pass=trim($auf[1]);
			if((static::$base)->exists($login)){
				$user=(static::$base)->get($login);
				if(Hash::verify($pass, $user[0])){
					$auf=array();
					$auf[0]=$login;
					$auf[1]=$user[0];
					$auf[2]=$user[1];
					$auf[3]=$user[2];
					$newh=Hash::rehash($pass, $user[0]);
					if($newh!=false){
						static::$base->set($login, array($newh, $user[1], $user[2]))->write();
					}
					return $auf;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		public static function getStatus($user){
			if(static::$base ==null){
				static::$base=new D_BASE();
			}
			if(!static::exists($user)){
				return "nostat";
			}
			$line=((static::$base)->get($user));
			return $line[1];
		}
		public static function getRealName($user){
			if(static::$base ==null){
				static::$base=new D_BASE();
			}
			if(!static::exists($user)){
				return "NoName";
			}
			$line=((static::$base)->get($user));
			return $line[2];
		}
	}
	class Stats{
		public static $can=null;
		private static $text=null;
		#region Level
		public static function levelDown($newstat){
			$newstat=trim($newstat);
			if(static::$can ==null){
				static::$can=new DBase("users", "can");
			}
			if(static::$can->exists($newstat)){
				$inc=0;
				$counted=(static::$can)->counted();
				$bool=false;
				foreach(static::$can as $namee=>$linee){
					if($bool){
						$newstat=$namee;
						break;
					}
					$inc++;
					if(($inc!=1)and($inc!=$counted)){
						if($namee==$newstat){
							$bool=true;
						}
					}
				}
				if($inc==$counted){
					return new Result("---users.impossibledownstatmin---");
				}
				$inc=1;
				$alles=array();
				$lastline=null;
				$lastname=null;
				foreach(static::$can as $name=>$line){
					if(($inc>2)and($name==$newstat)){
						$alles[$name]=$line;
						$alles[$lastname]=$lastline;
					}else{
						if($inc!=1){
							$alles[$lastname]=$lastline;
						}
					}
					$inc++;
					$lastname=$name;
					$lastline=$line;
				}
				$alles[$lastname]=$lastline;
				(static::$can)->setAll($alles)->write();
			}else{
				return new Result("---users.statusnotexists---");
			}
			return new Result();
		}
		public static function levelUp($newstat){
			$newstat=trim($newstat);
			if(Stats::$can ==null){
				Stats::$can=new DBase("users", "can");///look
			}
			if(Stats::$StatCanBaseAll ==null){
				Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
			}
			$lastline=null;
			$lastname=null;
			if(isset( Stats::$StatCanBaseAll [$newstat])){
				$inc=1;
				$All=array();
				foreach(Stats::$StatCanBaseAll as $name=>$line){
					if(($inc<=2)and($name==$newstat)){
						return new Result("Невозможно, позиция уже максимальна");
					}
					if(($inc>2)and($name==$newstat)){
						$All[$name]=$line;
						$All[$lastname]=$lastline;
					}else{
						if($inc!=1){
							$All[$lastname]=$lastline;
						}
					}
					$inc++;
					$lastname=$name;
					$lastline=$line;
				}
				$All[$lastname]=$lastline;
				Stats::$StatCanBase ->set_all($All);
				Stats::$StatCanBase ->write();
			}else{
				return new Result("Статуса не существует");
			}
			return new Result();
		}
		#endregion
		#region Groups
		public static function editTextGroup($status, $ops){
			if( static::$PravGroupsBase ==null){
				static::$PravGroupsBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablegroup.tdb");
			}
			if(! static::$PravGroupsBase ->exists_name($status)){
				return new Result("Пользователя не существует");
			}
			$status=trim($status);
			static::$PravGroupsBase->add_line(array($status, trim($ops)), $status);
			static::$PravGroupsBase->write();
			return new Result();
		}
		#endregion
		#region Stat
		public static function delete($newstat){
			$newstat=trim($newstat);
			if(Stats::$StatCanBase ==null){
				Stats::$StatCanBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tables.tdb");
			}
			if(Stats::$StatCanBaseAll ==null){
				Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
			}
			if(($newstat!="globaladmin")and($newstat!="admin")and($newstat!="bloger")){
				if(isset(Stats::$StatCanBaseAll[$newstat])){
					unset(Stats::$StatCanBaseAll[$newstat]);
					if( static::$StatTextBase ==null){
						static::$StatTextBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablei.tdb");
					}
					static::$StatTextBase ->del_name($newstat);
					static::$StatTextBase ->write();
				}
			}
			Stats::$StatCanBase ->set_all( Stats::$StatCanBaseAll );
			Stats::$StatCanBase ->write();
			return new Result();
		}
		public static function add($newstat, $ops){
			$newstat=trim($newstat);
			if(Stats::$StatCanBase ==null){
				Stats::$StatCanBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tables.tdb");
			}
			if(Stats::$StatCanBaseAll ==null){
				Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
			}
			if(!isset(Stats::$StatCanBaseAll [$newstat])){
				if( static::$StatTextBase ==null){
					static::$StatTextBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablei.tdb");
				}
				static::$StatTextBase ->add_line(trim($ops), $newstat);
				static::$StatTextBase ->write();
				foreach(Stats::$StatCanBaseAll ["globaladmin"] as $key=>$value){
					Stats::$StatCanBaseAll [$newstat][$key]="OFF";
				}
				Stats::$StatCanBase ->set_all(Stats::$StatCanBaseAll );
				Stats::$StatCanBase ->write();
			}
			return new Result();
		}
		public static function exists($newstat){
			$newstat=trim($newstat);
			if(Stats::$StatCanBase ==null){
				Stats::$StatCanBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tables.tdb");
			}
			if(Stats::$StatCanBaseAll ==null){
				Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
			}
			if(isset(Stats::$StatCanBaseAll [$newstat])){
				return true;
			}
			return false;
		}
		public static function editText($newstat, $ops){
			if( static::$StatTextBase ==null){
				static::$StatTextBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablei.tdb");
			}
			if(! static::$StatTextBase ->exists_name($newstat)){
				return new Result("Статуса несуществует");
			}
			static::$StatTextBase ->add_line($ops, $newstat);
			if( static::$StatTextBaseAll !=null){
				static::$StatTextBaseAll[$newstat]=$ops;
			}
			static::$StatTextBase ->write();
			return new Result();
		}
		public static function can($stat, $raz=354){
			if($raz===354){
				return Stats::can($GLOBALS['AUTH'][2], $stat);
			}
			if(is_string($raz)){
				if(strpos($raz, ',')){
					$raz=explode(',', $raz);
					foreach($raz as $rrr){
						if(Stats::can($stat, $rrr)){
							return true;
						}
					}
					return false;
				}
				if(strpos($raz, '.')){
					$raz=explode('.', $raz);
					foreach($raz as $rrr){
						if(!Stats::can($stat, $rrr)){
							return false;
						}
					}
					return true;
				}
				if(Stats::$StatCanBase ==null){
					Stats::$StatCanBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tables.tdb");
				}
				if(Stats::$StatCanBaseAll ==null){
					Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
				}
				if($stat=="globaladmin"){
					return true;
				}
				if(!isset(Stats::$StatCanBaseAll [$stat][$raz])){
					ELog::Logged("[DATA_BASE_ERROR] base: tables.tdb STAT_CAN_BASE; name: $stat\"$raz\",  in:".date("H:i:s").'&nbsp;'.date("d.m.Y"));
					return false;
				}
				if(Stats::$StatCanBaseAll [$stat][$raz]=="ON"){
					return true;
				}
				return false;
			}else{
				foreach($raz as $rrr){
					if(Stats::can($stat, $rrr)){
						return true;
					}
				}
				return false;
			}
		}
		#endregion
		#region GUI
		public static function HTMLStatuses(){
			if( Stats::$StatTextBase ==null){
				Stats::$StatTextBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablei.tdb");
			}
			if(Stats::$StatTextBaseAll==null){
				Stats::$StatTextBaseAll= Stats::$StatTextBase ->get_all();
			}
			$html="";
			foreach(Stats::$StatTextBaseAll as $us=>$ru){
				$html.="<option value=\"$us\">$ru</option>";
			}
			return($html);
		}
		public static function getRuStatus($stat){
			if( Stats::$StatTextBase ==null){
				Stats::$StatTextBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablei.tdb");
			}
			if(!Stats::$StatTextBase ->exists_name($stat)){
				return("Неизвестно");
			}
			return(Stats::$StatTextBase ->get_line($stat));
		}
		#endregion
		#region GLOBAL
		public static function setPravs(){
			if(Stats::$StatCanBase ==null){
				Stats::$StatCanBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tables.tdb");
			}
			if(Stats::$StatCanBaseAll ==null){
				Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
			}
			foreach(Stats::$StatCanBaseAll as $name=>$line){
				foreach($line as $key=>$value){
					if($name!="globaladmin"){
						Stats::$StatCanBaseAll [$name][$key]=(((isset($_POST[$name."_".$key]))and($_POST[$name."_".$key]=="ON"))?("ON"):("OFF"));
					}else{
						Stats::$StatCanBaseAll [$name][$key]="ON";
					}
				}
			}
			Stats::$StatCanBase->set_all(Stats::$StatCanBaseAll );
			Stats::$StatCanBase->write();
			return new Result();
		}
		#endregion
	}
	class Pravs{
		protected static $PravTextBase=null; //tablesi.tdb
		protected static $PravTextBaseAll=null;
		protected static $PravGroupsBase=null; //tablegroup.tdb
		protected static $PravGroupsBaseAll=null;
		#region PravGroup
		public static function addGroup($groupName, $ops){
			if( static::$PravGroupsBase ==null){
				static::$PravGroupsBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablegroup.tdb");
			}
			$groupName=trim($groupName);
			static::$PravGroupsBase ->add_line(array($groupName, trim($ops)), $groupName);
			static::$PravGroupsBase ->write();
			return new Result();
		}
		public static function deleteGroup($groupName){
			if( static::$PravGroupsBase ==null){
				static::$PravGroupsBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablegroup.tdb");
			}
			if(! static::$PravGroupsBase ->exists_name($groupName)){
				return new Result("Группы не существует");
			}
			static::$PravGroupsBase ->del_name(trim($groupName));
			static::$PravGroupsBase ->write();
			return new Result();
		}
		#endregion
		#region Prav
		public static function editText($newstat, $ops, $group){
			$newstat=trim($newstat);
			if(Pravs::$PravTextBase ==null){
				Pravs::$PravTextBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablesi.tdb");
			}
			if(Pravs::$PravTextBaseAll ==null){
				Pravs::$PravTextBaseAll= Pravs::$PravTextBase ->get_all();
			}
			Pravs::$PravTextBase ->add_line(array(trim($ops), trim($group)), $newstat);
			Pravs::$PravTextBase ->write();
			return new Result();
		}
		public static function add($name, $group, $ops){
			$name=trim($name);
			if( Pravs::$PravTextBase ==null){
				Pravs::$PravTextBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablesi.tdb");
			}
			Pravs::$PravTextBase ->add_line(array(trim($ops), trim($group)), $name);
			Pravs::$PravTextBase ->write();
			if(Stats::$StatCanBase ==null){
				Stats::$StatCanBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tables.tdb");
			}
			if(Stats::$StatCanBaseAll ==null){
				Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
			}
			foreach(Stats::$StatCanBaseAll as $namem=>$line){
				$line[$name]=(($namem=="globaladmin")?("ON"):("OFF"));
				Stats::$StatCanBaseAll [$namem]=$line;
			}
			Stats::$StatCanBase ->set_all(Stats::$StatCanBaseAll);
			Stats::$StatCanBase ->write();
			return new Result();
		}
		public static function delete($prav){
			if( Pravs::$PravTextBase ==null){
				Pravs::$PravTextBase =new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablesi.tdb");
			}
			$prav=trim($prav);
			if(Pravs::$PravTextBase ->exists_name($prav)){
				Pravs::$PravTextBase ->del_name($prav);
				Pravs::$PravTextBase ->write();
			}
			if(Stats::$StatCanBase ==null){
				Stats::$StatCanBase=new D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tables.tdb");
			}
			if(Stats::$StatCanBaseAll ==null){
				Stats::$StatCanBaseAll= Stats::$StatCanBase ->get_all();
			}
			foreach(Stats::$StatCanBaseAll as $name=>$line){
				if(isset($line[$prav])){
					unset(Stats::$StatCanBaseAll [$name][$prav]);
				}
			}
			Stats::$StatCanBase ->set_all(Stats::$StatCanBaseAll);
			Stats::$StatCanBase ->write();
			return new Result();
		}
		#endregion
	}
}
?>