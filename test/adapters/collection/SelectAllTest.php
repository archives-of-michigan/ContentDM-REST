<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'test_helper'));
require_once File.join('adapters','collection');
 
class Adapters_Collection_SelectAllTest extends PHPUnit_Framework_TestCase
{
    public function testShouldReturnAllCollections()
    {
        $collections = new Collection();
        $test_num_collections = sizeof($collections->select_all());
        $actual_num_collections = sizeof(dmGetCollectionList());
        
        $this->assertEqual($num_collections, $test_num_collections);
    }
}
?>