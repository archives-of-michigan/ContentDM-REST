<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','test_helper.php'));
require_once 'collectionsgateway.php';
 
class CollectionsGatewaySelectAllTest extends PHPUnit_Framework_TestCase
{
    public function testShouldReturnAllCollections()
    {
        $collections = new CollectionsGateway();
        $test_num_collections = sizeof($collections->all());
        $actual_num_collections = sizeof(dmGetCollectionList());
        
        $this->assertEquals($actual_num_collections, $test_num_collections);
    }
}