<?php
echo("<style>*{background:#333;color:eee}</style>");
echo(str_repeat(" ", 999999));
include(rtrim(str_replace("\\", "/", __DIR__), "/")."/core/include.php");
\LCMS\Core\Page::CMS();
echo("O");
exit;
if(Users::can("shop")){?>
<div style="float: right; margin: 5px; margin-right: 8px;">
<a href="shop.php">
<img src="/cms/pic/shop.png" style="float: none;" />
<div class="podpis">
Магазин
</div>
</a>
</div>
<?php } ?>
<p style="font-size: larger;"><?php l('hello'); ?>, <?php
echo(htmlchars($GLOBALS['AUTH'][0]));?>.<br>
<?php l('your-status'); ?>: <?php
echo(Stats::getRuStatus($GLOBALS['AUTH'][2]));?>.</p>
<?php
echo('<a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'">Перейти на сайт</a>');
?>
<?php if(Stats::can("editus")){?>
<h3>Все пользователи</h3>
<?php
$mydb=new \LCMS\MainModules\D_BASE($_SERVER['DOCUMENT_ROOT']."/cms/tablei.tdb");
$myall=$mydb->get_all();
$db=new \LCMS\MainModules\D_BASE();
$all=$db->get_all();
txt("<table><tr><th>---username---</th><th>---password---&nbsp;(hash)</th><th>---status---</th><th>Действия</th><th>Сменить пароль</th><th>Войти под именем</th><th>Продвинутость</th></tr>");
foreach($all as $name=>$line){
	$del='';
	$del.=new Action("del", array('name'=>'"'.addslashes($name).'"'));
	$del.=new Action("levelup", array('name'=>'"'.addslashes($name).'"'));
	$del.=new Action("leveldown", array('name'=>'"'.addslashes($name).'"'));
	$del.='</td><td>';
	$del.=new Action("chan_pass", array('name'=>'"'.addslashes($name).'"'));
	$del.='</td><td>';
	$del.=new Action("enterin", array('name'=>'"'.addslashes($name).'"'));
	$del.='</td><td>';
	$del.=new Action("setcleverall", array('name'=>'"'.addslashes($name).'"'));
	echo("<tr><td><a class=\"an\" name=\"$name\"></a><span>$name</span></td><td>".$line[0]."</td><td>".((isset($myall[$line[1]]))?($myall[$line[1]]):($line[1]))."</td><td>$del</td></tr>");
}
echo("</table>");
?>
<script>
window.addEventListener("load", function(){
	var c=document.getElementsByClassName("an");
	for(var i=0; i<c.length; i++){
		if(("#"+c[i].name)==window.location.hash){
			c[i].nextSibling.style.color="#ff4444";
		}
	}
	if(window.location.hash=="#fun"){
		var oo=0;
		window.setInterval(function(){
			if(window.location.hash=="#fun"){
				document.body.style.filter="hue-rotate("+oo+"deg)";
				oo+=3;
			}
		}, 6);
	}
}, false);
</script>
<a href="table.php"><?php l('status-management'); ?></a>
<?php } ?>
<?php if(Stats::can("actlog")){ ?>
<br>
<a href="actlog.php"><img src="/cms/pic/actlog.png" title="Лог действий" />Лог действий</a>
<?php
}
Action::e('add');
?>
<?php if(Stats::can("logo,obnov,feedback,speed,enter")){ ?>
<div style="margin: 20px;">
<a target="_self" style="font-size: 20pt;" href="about.php"><img src="/cms/pic/cmslmini.png" title="О системе" />О системе</a>
</div>
<?php
}
Page::footer()?>
