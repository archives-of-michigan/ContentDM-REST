<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'test_helper.php'));

require_once file_join('PHPUnit','Framework.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionCalculateZoomLevelsTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionImageSettingsTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionAllTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','field','CollectionFieldInitializeTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','field','CollectionFieldFindAllByAliasTest.php');

class AllTests {
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite;
    $suite->addTestSuite('CollectionTest');
    $suite->addTestSuite('CollectionCalculateZoomLevelsTest');
    $suite->addTestSuite('CollectionImageSettingsTest');
    $suite->addTestSuite('CollectionAllTest');
    $suite->addTestSuite('CollectionFieldInitializeTest');
    $suite->addTestSuite('CollectionFieldFindAllByAliasTest');
    
    return $suite;
  }
}