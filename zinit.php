<?php
define('ROOT_DIR',$_SERVER['DOCUMENT_ROOT']);
define('CLASSES_DIR','classes');
define('TEMPLATES_DIR','templates');
define('HANDLERS_DIR','handlers');
// тест
define('Z_DIR','z');
define('ZREPORTS_DIR','zreports');
// административная панель
define('ADMIN_DIR','administrator');

set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR);
set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR.DIRECTORY_SEPARATOR.CLASSES_DIR);
set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR.DIRECTORY_SEPARATOR.TEMPLATES_DIR);
set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR.DIRECTORY_SEPARATOR.HANDLERS_DIR);
// тест
set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR.DIRECTORY_SEPARATOR.Z_DIR);
set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR.DIRECTORY_SEPARATOR.ZREPORTS_DIR);
// административная панель
set_include_path(get_include_path().PATH_SEPARATOR.ROOT_DIR.DIRECTORY_SEPARATOR.ADMIN_DIR);

spl_autoload_register(function ($class) {include ($class .'.class.php');});
?>