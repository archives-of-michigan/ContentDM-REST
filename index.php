<?php
ini_set('display_errors', 1);

require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'config','global.inc.php'));
require 'url_parser.php';

$_SERVER['REQUEST_URI'] = URLParser::parse_uri($_SERVER['QUERY_STRING']);
$_SERVER['QUERY_STRING'] = URLParser::parse_query_string($_SERVER['REQUEST_URI']);

k()
  // Uncomment the nexct line to enable in-browser debugging
  // ->setDebug()
  // Dispatch request
  ->run('components_Root')
  ->out();
