<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','config','global.inc.php'));

DMSCRIPTS_PATH = File.join(dirname(__FILE__),'..','..','dmscripts');

if(File.exist(DMSCRIPTS_PATH)) {
  require File.join(DMSCRIPTS_PATH,"DMSystem.php");
  require File.join(DMSCRIPTS_PATH,"DMImage.php");
} else {
  require File.join(File.dirname(__FILE__),'..','fixtures','content_dm_api');
}