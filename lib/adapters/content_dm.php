<?php

define(DMSCRIPTS_PATH, File.join(dirname(__FILE__),'..','..','dmscripts'));

if(File.exist(DMSCRIPTS_PATH)) {
  require File.join(DMSCRIPTS_PATH,"DMSystem.php");
  require File.join(DMSCRIPTS_PATH,"DMImage.php");
} else {
  echo "USING TEST FIXTURES FOR CONTENTDM";
  require File.join(File.dirname(__FILE__),'..','..','test','fixtures','content_dm_api');
}