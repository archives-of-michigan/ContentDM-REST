<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'test_helper'));
require_once File.join('PHPUnit','Framework.php');
require_once File.join(dirname(__FILE__),'adapters','collection','SelectAllTest');
require_once File.join(dirname(__FILE__),'adapters','collection','SelectSingleTest');
 
class AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
        $suite->addTestSuite('SelectAllTest');
        $suite->addTestSuite('SelectSingleTest');
        return $suite;
    }
}
?>