<?php
// error_reporting(E_ALL | E_STRICT);

set_include_path(
  get_include_path().PATH_SEPARATOR.
  join(PATH_SEPARATOR,
    array(
      dirname(__FILE__).'/../vendor/konstrukt/lib',
      dirname(__FILE__).'/../lib',
      dirname(__FILE__).'/..'
    )
  )
);

require_once 'util.php';

require_once 'konstrukt/konstrukt.inc.php';

date_default_timezone_set('US/Eastern');
set_error_handler('k_exceptions_error_handler');
spl_autoload_register('k_autoload');