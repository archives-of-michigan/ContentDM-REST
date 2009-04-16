<?php
require_once file_join(dirname(__FILE__),'..','config','global.inc.php');
k()
  // Uncomment the nexct line to enable in-browser debugging
  ->setDebug()
  // Dispatch request
  ->run('components_Root')
  ->out();
