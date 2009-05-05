<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','..','test_helper.php'));
require_once file_join('models','collection.php');
 
class CollectionTest extends PHPUnit_Framework_TestCase
{
  public function testShouldInitialize()
  {
    $item = array('alias' => '/p12345', 'name' => 'Musical History', 'path' => 'D:\foo\bar');
    
    $collection = new Collection($item);
    
    $this->assertEquals('/p12345', $collection->alias);
    $this->assertEquals('Musical History', $collection->name);
    $this->assertEquals('D:\foo\bar', $collection->path);
  }
}