<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','..','..','test_helper.php'));
require_once file_join('models','collection','image_setting.php');
 
class CollectionImageSettingCalculateZoomLevelsTest extends PHPUnit_Framework_TestCase
{
  public function testShouldCombineDefaultZoomLevelsWithDocviewer()
  {
    throw new PHPUnit_Framework_IncompleteTestError(
      'This test has not been implemented yet.'
    );
  }
}