<?php
function file_join(*args) {
  return join(DIRECTORY_SEPARATOR,func_get_args());
}

error_reporting(E_ALL | E_STRICT);
set_include_path(
  get_include_path()
  . PATH_SEPARATOR . file_join(dirname(__FILE__),'..','vendor','konstrukt','lib');

require_once file_join('konstrukt','konstrukt.inc.php');

date_default_timezone_set('US/Eastern');
set_error_handler('k_exceptions_error_handler');
spl_autoload_register('k_autoload');