<?php
require_once join(DIRECTORY_SEPARATOR, array(dirname(__FILE__),'..','..','..','test_helper.php'));
require_once file_join('components','collections','list.php');
 
class components_collections_list_test extends PHPUnit_Framework_TestCase {
  
  public function testShouldRenderJSON() {
    $component = new components_collections_list;
    $json = $component->renderJson();
    $obj = json_decode($json);
    
    $this->assertTrue(is_array($obj));
  }
}