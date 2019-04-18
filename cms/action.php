<?php
namespace LCMS\Core{
	include(__DIR__ ."/cmsinclude.php");
	function goback(){
		echo("<p style=\"font-size: 14pt; font-family: monospace; text-align: center;\"><a href=\"".htmlamp($_POST['page'])."\">Назад</a></p>".'<script> document.body.addEventListener("keydown", function(evt){ if(evt.keyCode==13){window.location.replace("'.htmlamp($_POST['page']).'");}}, false);</script>');
	}
	function alert($text){
		acte($text, "ff3333");
	}
	function accepted($text){
		acte($text, "33ff33");
	}
	function acte($text, $color){
		echo("<p style=\"font-size: 24pt; font-family: monospace; color: #".$color."; text-align: center;\"><b>".Text::parse($text, TEXT::PARSE_LOCALE)."</b></p>");
	}
	AntiXSS::H();
	AntiXSS::R();
	Web::headerEncode();
	$last=isset($_GET['last']);
	if(!isset($_POST['page'])){
		$_POST['page']="/index.php";
	}
	if(User::authHasnt()){
		header("Location: http://".$_SERVER['HTTP_HOST']."/index.php");
		exit;
	}
	if($last){
		if(Path::exists(Path::tmp("last_act_".User::authName().".seria"))){
			$_POST=array_merge(uncode(Path::tmp("last_act_".User::authName().".seria"), array()), $_POST);
			Path::delete(Path::tmp("last_act_".User::authName().".seria"));
		}else{
			alert("---tmpfiledeleted---");
			goback();
			ob_super_end_flush();
			exit;
		}
	}
	if((!isset($_POST['ZZZ_OF']))or(!Salt::compare($_POST['ZZZ_OF']))){
		alert("---secerror--- [".((isset($_POST['tsel']))?(html($_POST['tsel'])):(''))."]<img src=\"/media/sec.png\"></img>");
		goback();
		ob_super_end_flush();
		exit;
	}
	if(isset($_POST['UNKEY'])and(!$last)){
		if(!Counter::get($_POST['UNKEY'])){
			Path::put(Path::tmp("last_act_".User::authName().".seria"), serialize($_POST));
			alert('Действия должны иметь строгий хронологический порядок. Это сделано для безопасности. Вы сможите повторить ваше действие позже, но помните: в случае изменения баз в промежуток времени от загрузки прошлой страницы до текущего момента может произойти ошибка структуры этих баз. <a href="/cms/error.php">Отключить</a>. <a href="/cms/action.php?last=1">Повторить</a>.');
			goback();
			ob_super_end_flush();
			exit;
		}
	}
	$count=5;
	while(!Locker::set()){
		if(($count--)<=0){
			Path::put(Path::tmp("last_act_".User::authName().".seria"), serialize($_POST));
			alert("Установлен замок! Т.Е. Вы произвели действие, пока выполняется другое действие. Если вы считаете, что это ошибка, подождите 2 минуты, после чего сообщите разработчику, спасибо.");
			goback();
			ob_super_end_flush();
			exit;
		}else{
			sleep(1);
		}
	}
	$_POST['tsel']=trim(str_replace("..", "", $_POST['tsel']));
	$str=$_POST['tsel'];
	$result=null;
	if(Action::exists($str)){
		ALog::add($str);
		$act=(new Action($str));
		ob_start();
		$result=$act->i();
		Locker::unlock();
		if($result->ok()){
			if($result->download()){
				ob_super_end_flush();
				exit;
			}
			header("Location: http://".$_SERVER['HTTP_HOST'].htmlamp($_POST['page']));
			exit;
		}
	}else{
		$result=new Result("---actnotexists--- (".html($_POST['tsel']).")");
	}
	if($result->ok()){
		header("Location: http://".$_SERVER['HTTP_HOST'].htmlamp($_POST['page']));
		exit;
	}else{
		alert($result->get());
		goback();
		ob_super_end_flush();
		exit;
	}
	exit;
}

