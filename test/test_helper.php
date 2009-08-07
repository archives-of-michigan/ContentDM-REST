<?php
set_include_path(
  get_include_path().PATH_SEPARATOR.
  join(PATH_SEPARATOR, array(dirname(__FILE__)))
);
require_once 'simpletest/autorun.php';

if(TextReporter::inCli()) {
  SimpleTest :: prefer(new TextReporter());
} else {
  SimpleTest :: prefer(new HtmlReporter());
}

require_once dirname(__FILE__).'/../config/global.inc.php';