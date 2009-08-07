<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once 'util.php';

// URLParser.parse_uri
class lib_util_dir_glob extends UnitTestCase {
  public function testShouldReturnRecursiveListOfPhpFiles() {
    $glob = Dir::glob(dirname(__FILE__).'/../../vendor/konstrukt/lib', TRUE, '/.*\.php$/');
    $basenames = array();
    foreach($glob as $f) { $basenames[] = basename($f); }
    $this->assertEqual(
      $basenames,
      array('adapter.inc.php','charset.inc.php','konstrukt.inc.php','logging.inc.php','virtualbrowser.inc.php'));
  }
  
  public function testShouldReturnSingleDirectory() {
    $glob = Dir::glob(dirname(__FILE__).'/../../vendor/konstrukt/lib/konstrukt/*');
    $basenames = array();
    foreach($glob as $f) { $basenames[] = basename($f); }
    $this->assertEqual(
      $basenames,
      array('adapter.inc.php','charset.inc.php','konstrukt.inc.php','logging.inc.php','virtualbrowser.inc.php'));
  }
}
