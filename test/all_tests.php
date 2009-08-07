<?php
error_reporting(E_ALL);
require_once dirname(__FILE__).'/test_helper.php';

require_once dirname(__FILE__).'/simpletest/unit_tester.php';
require_once dirname(__FILE__).'/simpletest/reporter.php';

class AllTests extends TestSuite {
  function AllTests() {
    $this->TestSuite('All tests');
    foreach(Dir::glob(dirname(__FILE__).'/lib', TRUE, '/.*\.php/') as $filename) {
      $this->addFile($filename);
    }
  }
}
