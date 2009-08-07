<?php
require_once dirname(__FILE__).'/../../../test_helper.php';
require_once 'components/collections/entity.php';
require_once 'konstrukt/virtualbrowser.inc.php';

class components_collections_Entity_test extends WebTestCase {
  function createBrowser() {
    return new k_VirtualSimpleBrowser('components_collections_Entity');
  }
  
  public function testShouldRenderJSON() {
    $this->addHeader('Accept: application/json,*/*;q=0.8');
    $this->assertTrue($this->get('/?q=/collections/p4006coll2'));
    $this->assertResponse(200);
    $this->assertMime('application/json; charset=utf-8');
    
    $content = $this->getBrowser()->getContent();
    $this->assertNotEqual('null', $content);
    
    $obj = json_decode($content);
    
    $this->assertEqual('Governors of Michigan', $obj['name']);
  }
}