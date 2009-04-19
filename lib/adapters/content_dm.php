<?php

define(DMSCRIPTS_PATH, file_join(dirname(__FILE__),'..','..','dmscripts'));

if(file_exists(DMSCRIPTS_PATH)) {
  require file_join(DMSCRIPTS_PATH,"DMSystem.php");
  require file_join(DMSCRIPTS_PATH,"DMImage.php");
} else {
  echo "USING TEST FIXTURES FOR CONTENTDM";
  require file_join(dirname(__FILE__),'..','..','test','fixtures','content_dm_api');
}