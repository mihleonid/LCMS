<?php
namespace LCMS\Core{
	class PageLog extends IPageLog{
		public static function put($path, $user, $type, $ok=true){
			switch($type){
				case (static::ADD):
					$type='<b style="color: #00aa00">Создание</b>';
					break;
				case (static::EDIT):
					$type='<b style="color: #0000aa">Редактирование</b>';
					break;
				case (static::DELETE):
					$type='<b style="color: #aa0000">Удаление</b>';
					break;
				default:
					$type='<b style="color: #000000">Неизвестно</b>';
					break;
			}
			if($ok){
				$ok='<span style="color: #00aa00;">Успешно</span>';
			}else{
				$ok='<span style="color: #aa0000;">Ошибка</span>';
			}
			$user=User::realName($user);
			$type="$path|$user|$type|$ok";
			Log::llog(Path::cms("page.log"), $type);
		}
	}
}
?>