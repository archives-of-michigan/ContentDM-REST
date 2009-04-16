<?
DMSCRIPTS_PATH = join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'test_helper'));

if(file_exists(DMSCRIPTS_PATH)) {
  require join(DIRECTORY_SEPARATOR, array(DMSCRIPTS_PATH,"DMSystem.php"));
  require join(DIRECTORY_SEPARATOR, array(DMSCRIPTS_PATH,"DMImage.php"));
} else {
  require join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','fixtures','content_dm_api'));
}
?>