<?php

define('DMSCRIPTS_PATH', dirname(__FILE__).'/../../dmscripts');

if(file_exists(DMSCRIPTS_PATH)) {
  require DMSCRIPTS_PATH."/DMSystem.php";
  require DMSCRIPTS_PATH."/DMImage.php";
} else {
  require dirname(__FILE__).'/../test/fixtures/content_dm_api.php';
}