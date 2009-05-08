<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','..','..','test_helper.php'));
require_once file_join('models','collection','field.php');
 
class CollectionFieldFindAllByAliasTest extends PHPUnit_Framework_TestCase {
  
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
    
    $this->assertEquals(1, count($collection_fields));
  }
  
  public function testShouldReturnArrayOfCollectionFields() {
    foreach(CollectionField::find_all_by_alias('foo') as $field) {
      $this->assertType(CollectionField, $field);
    }
  }
}