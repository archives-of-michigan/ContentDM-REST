<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once 'models/collection.php';
 
class collection_test extends UnitTestCase {
  public function testShouldInitialize()
  {
    $item = array('alias' => '/p12345', 'name' => 'Musical History', 'path' => 'D:\foo\bar');
    
    $collection = new Collection($item);
    
    $this->assertEqual('/p12345', $collection->alias);
    $this->assertEqual('Musical History', $collection->name);
    $this->assertEqual('D:\foo\bar', $collection->path);
  }
}

class collection_all_test extends UnitTestCase {
  public function testShouldReturnAllCollections()
  {
    $test_num_collections = sizeof(Collection::all());
    $actual_num_collections = sizeof(dmGetCollectionList());
    
    $this->assertEqual($actual_num_collections, $test_num_collections);
  }
}

class collection_find_test extends UnitTestCase {
  public function testShouldFindColelctionWithoutSlash() {
    $collection = Collection::find('p4006coll4');
    $this->assertNotNull($collection);
    $this->assertEqual('Early Photography', $collection->name);
  }
  
  public function testShouldFindColelctionWithSlash() {
    $collection = Collection::find('/p4006coll4');
    $this->assertNotNull($collection);
    $this->assertEqual('Early Photography', $collection->name);
  }
  
  public function testShouldReturnNullIfCollectionNotFound() {
    $collection = Collection::find('p1111coll4');
    $this->assertNull($collection);
  }
}