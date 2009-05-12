<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'test_helper.php'));

require_once file_join('PHPUnit','Framework.php');

require_once file_join(dirname(__FILE__),'lib','components','collections','components_collections_list_test.php');

require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionAllTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','field','CollectionFieldInitializeTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','field','CollectionFieldFindAllByAliasTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','image_setting','CollectionImageSettingTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','image_setting','CollectionImageSettingCalculateZoomLevelsTest.php');

class AllTests {
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite;
    
    $suite->addTestSuite('components_collections_list_test');
    
    $suite->addTestSuite('CollectionTest');
    $suite->addTestSuite('CollectionImageSettingCalculateZoomLevelsTest');
    $suite->addTestSuite('CollectionImageSettingTest');
    $suite->addTestSuite('CollectionAllTest');
    $suite->addTestSuite('CollectionFieldInitializeTest');
    $suite->addTestSuite('CollectionFieldFindAllByAliasTest');
    
    return $suite;
  }
}