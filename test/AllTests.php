<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'test_helper.php'));

require_once file_join('PHPUnit','Framework.php');
require_once file_join(dirname(__FILE__),'lib','CollectionsGateway','CollectionsGatewaySelectAllTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionTest.php');
require_once file_join(dirname(__FILE__),'lib','models','collection','CollectionFieldInfoTest.php');
 
class AllTests {
  public static function suite() {
    $suite = new PHPUnit_Framework_TestSuite;
    $suite->addTestSuite('CollectionsGatewaySelectAllTest');
    $suite->addTestSuite('CollectionTest');
    $suite->addTestSuite('CollectionFieldInfoTest');
    return $suite;
  }
}