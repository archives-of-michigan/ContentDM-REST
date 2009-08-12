<?php
ini_set('display_errors', 1);

require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'config','global.inc.php'));

k()
  // Uncomment the nexct line to enable in-browser debugging
  // ->setDebug()
  ->enableFrontController()
  // Dispatch request
  ->run('components_Root')
  ->out();
