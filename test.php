<?php
namespace L{
spl_autoload_register(function ($c){echo($c."<br>"); 
	eval("?><?php class $c{}?>");
	});
}
namespace{
    new C;
}
namespace A{
	new C;
}
?>