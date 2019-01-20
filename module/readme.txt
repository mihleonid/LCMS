/*
 * This module is Free.
 * You can modify it.
 */
actions/ optional directory of actions
data/ - optional directory for custom data
include.php - file to be included
readme.txt - file with info, will be deleted after installation
main.log - if not exists or empty - this is good. use module 'log' for putting messages
info.txt - header information see 'configuration format'
logo.png logotype of module

localeN.txt    |     localization see 'localization format'
imgN.png       |}=>  imgage of page
pageN.html     |     extended html (localization ext; actions ext) will be renamed to *.php and surrownded with inclusion.
pinfoN.txt     |     rules of building page see 'page building format' optional. Must contain inclusion info.

actions/***.php|}=> action 
