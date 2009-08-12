<?php
require_once dirname(__FILE__).'/../../../test_helper.php';
require_once 'components/collections/list.php';
require_once 'konstrukt/virtualbrowser.inc.php';

class components_collections_List_test extends WebTestCase {
  function createBrowser() {
    return new k_VirtualSimpleBrowser('components_Root');
  }
  
  function test_list() {
    $this->assertTrue($this->get('/collections'));
    $this->assertResponse(200);
    $this->assertMime('text/html; charset=utf-8');
    $this->assertText("Collections");
  }
  
  function test_json_representation() {
    $this->addHeader('Accept: application/json,*/*;q=0.8');
    $this->assertTrue($this->get('/collections'));
    $this->assertResponse(200);
    $this->assertMime('application/json; charset=utf-8');
    
    $content = $this->getBrowser()->getContent();
    $this->assertNotEqual(null, $content);
    
    $obj = json_decode($content);
    
    $this->assertTrue(is_array($obj));
    $this->assertEqual(13, count($obj));
  }
}