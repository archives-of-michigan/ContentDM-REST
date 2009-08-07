<?php
require_once dirname(__FILE__).'/../../../../test_helper.php';
require_once 'models/collection/field.php';
 
class CollectionFieldFindAllByAliasTest extends UnitTestCase {
  
  public function testShouldGetAllFields() {
    $fields = array(
      array(
        'nick' => 'subj',
        'name' => 'Subject',
        'size' => '55',
        'search' => '1',
        'hide' => '0')
    );
    
    // $stub->method('dmGetCollectionFieldInfo')->will(PHPUnit_Framework_MockObject_Stub_Return($fields));
    
    $collection_fields = CollectionField::find_all_by_alias('foo');
    
    $this->assertEqual(1, count($collection_fields));
  }
  
  public function testShouldReturnArrayOfCollectionFields() {
    foreach(CollectionField::find_all_by_alias('foo') as $field) {
      $this->assertIsA($field, 'CollectionField');
    }
  }
}