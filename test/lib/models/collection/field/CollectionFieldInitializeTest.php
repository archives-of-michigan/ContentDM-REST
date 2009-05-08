<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','..','..','test_helper.php'));
require_once file_join('models','collection','field.php');
 
class CollectionFieldInitializeTest extends PHPUnit_Framework_TestCase
{
  public function testShouldInitialize()
  {
    $info = array(
      'nick' => 'subj',
      'name' => 'Subject',
      'size' => '55',
      'search' => '1',
      'hide' => '0'
    );
    
    $field_info = new CollectionField($info);
    
    $this->assertEquals('subj', $field_info->nick);
    $this->assertEquals('Subject', $field_info->name);
    $this->assertEquals('55', $field_info->size);
    $this->assertEquals('1', $field_info->search);
    $this->assertEquals('0', $field_info->hide);
  }
}