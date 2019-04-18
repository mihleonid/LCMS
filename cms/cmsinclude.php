<?php
ini_set('memory_limit','536870912');
error_reporting(-1);
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$_SERVER['DOCUMENT_ROOT']="/var/www/site/public_html/";
//include(__DIR__ ."/fix.php");
include(__DIR__ ."/exception.php");
include(__DIR__ ."/core/include.php");
//\LCMS\Core\Enviroment\Timezone::date();
\LCMS\Core\CMS::initialize();

//(\LCMS\Core\User::auth())
if((\LCMS\Core\User::authHas())and isset($_COOKIE['sudo'])){
	if(isset($_GET['i'])){
		setcookie('sudo', '', 1);
	}else{
		if( \LCMS\Core\Users\Stats::can('users')){
			$sudo=explode('-', $_COOKIE['sudo']);
			for($i=0;$i<2;$i++){
				if(!isset($sudo[$i])){
					goto start;
				}
			}
			if(isset($sudo[3])){
				goto start;
			}
			//(\LCMS\Core\User::auth())=$sudo;//todo usersudo
			\LCMS\Core\Page::sudo();
		}
	}
}
start:;

