<?php
error_reporting(E_ALL | E_STRICT);
set_include_path(
  get_include_path()
  . PATH_SEPARATOR . dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.
                     'vendor'.DIRECTORY_SEPARATOR.'konstrukt'.DIRECTORY_SEPARATOR.'lib'
  . PATH_SEPARATOR . dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'lib');

require_once 'id_rather_be_coding_ruby';
require_once File.join('konstrukt','konstrukt.inc.php');

date_default_timezone_set('US/Eastern');
set_error_handler('k_exceptions_error_handler');
spl_autoload_register('k_autoload');