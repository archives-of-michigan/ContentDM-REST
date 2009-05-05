<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','..','test_helper.php'));
require_once file_join('models','collection.php');
 
class CollectionFieldInfoTest extends PHPUnit_Framework_TestCase
{
  public function testShouldGetFieldInfo()
  {
    $item = array('alias' => '/p12345', 'name' => 'Musical History', 'path' => 'D:\foo\bar');
    $collection = new Collection($item);
    
    $field_info = $collection->field_info();
    
    $this->assertEquals('/p12345', $collection->alias);
    $this->assertEquals('Musical History', $collection->name);
    $this->assertEquals('D:\foo\bar', $collection->path);
    
    $field_info = $collection->field_info();
    $this->assertEquals('title', $field_info[0]['nick']);
    $this->assertEquals('Title', $field_info[0]['name']);
    $this->assertEquals('50', $field_info[0]['size']);
    $this->assertEquals('1', $field_info[0]['search']);
    $this->assertEquals('0', $field_info[0]['hide']);
  }
}