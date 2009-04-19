<?php
error_reporting(E_ALL | E_STRICT);
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','lib','id_rather_be_coding_ruby.php'));

set_include_path(
  get_include_path().join(PATH_SEPARATOR,
    array(
      File.join(File.dirname(__FILE__),'..','vendor','konstrukt','lib'),
      File.join(File.dirname(__FILE__),'..','lib')
    )
  )
);

require_once File.join('konstrukt','konstrukt.inc.php');

date_default_timezone_set('US/Eastern');
set_error_handler('k_exceptions_error_handler');
spl_autoload_register('k_autoload');