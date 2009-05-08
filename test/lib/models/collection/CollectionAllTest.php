<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','..','test_helper.php'));
require_once file_join('models','collection.php');
 
class CollectionAllTest extends PHPUnit_Framework_TestCase
{
    public function testShouldReturnAllCollections()
    {
        $test_num_collections = sizeof(Collection::all());
        $actual_num_collections = sizeof(dmGetCollectionList());
        
        $this->assertEquals($actual_num_collections, $test_num_collections);
    }
}