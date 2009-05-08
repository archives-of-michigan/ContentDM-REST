<?php
error_reporting(E_ALL | E_STRICT);
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','lib','id_rather_be_coding_ruby.php'));

set_include_path(
  get_include_path().PATH_SEPARATOR.
  join(PATH_SEPARATOR,
    array(
      file_join(dirname(__FILE__),'..','vendor','konstrukt','lib'),
      file_join(dirname(__FILE__),'..','lib'),
      file_join(dirname(__FILE__),'..')
    )
  )
);

require_once file_join('konstrukt','konstrukt.inc.php');

date_default_timezone_set('US/Eastern');
set_error_handler('k_exceptions_error_handler');
spl_autoload_register('k_autoload');