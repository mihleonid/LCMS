///
 * This program is free software; you can't modify it. You can copy it. And use standart updater and moduler for modification.
 * 
///
 *
 * Т.е. ядро системы является бесплатным и не может быть изменено напрямую,
 * но модули, расширения и плагины (также как темы, фоны, части, шаблоны, скриптовые модули)
 * могут распространяться под другими соглашениями.
///

CMS Leonid - програмное обеспечение для создания сайтов. Существует множество версий:
 1. Ранняя версия. Полностью готова к использованию, но функциональность не обширна.
 2. Вторая версия. Модульная структура. Не закончена (бета версия).
 3. Третья версия. Модульная структура. Активная разработка.
Эта система универсальна и имеет огромную функциональность. Для получения дополнительной информации смотрите 'Документация' и 'Документация для разработчиков'.
Мы будем рады рад, если Вы сообщите нам о ошибках и неточностях в модулях и ядре.
Сейчас разработку ведёт один человек.

Немного о структуре PHP кода:
CMS использует PHP. Имеется встроенный (псевдо)компилятор, который оптимизирует и очищает код. В тестовой JIT версии он испльзуется для вызова настоящего компилятора. Поддерживаемые директивы:
%COMPIELER:RANDOM% - вставить случайное число.
%COMPIELER:NOTOUCH% - не трогать файл (не гарантирована работа в модулях).
