<?php
require_once dirname(__FILE__).'/../../../../test_helper.php';
require_once 'models/collection/field.php';
 
class CollectionFieldInitializeTest extends UnitTestCase
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
    
    $this->assertEqual('subj', $field_info->nick);
    $this->assertEqual('Subject', $field_info->name);
    $this->assertEqual('55', $field_info->size);
    $this->assertEqual('1', $field_info->search);
    $this->assertEqual('0', $field_info->hide);
  }
}