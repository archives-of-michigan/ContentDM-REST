<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'response.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'smartyresource.php';

/**
 * These tests test the Smarty resource.
 * @package Tonic/Tests
 * @version $Revision: 36 $
 */
class TestSmartyResource extends UnitTestCase
{
	var $adapter;
	
	var $mimetypes = array( // mimetype to file extension map
		'html' => 'text/html',
		'txt' => 'text/plain',
		'php' => 'application/php',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'text/xml',
		'rss' => 'application/rss+xml',
		'atom' => 'application/atom+xml',
		'gz' => 'application/x-gzip',
		'tar' => 'application/x-tar',
		'zip' => 'application/zip',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'ico' => 'image/x-icon',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',
		'avi' => 'video/mpeg',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mov' => 'video/quicktime',
		'mp3' => 'audio/mpeg'
	);
	
    function testSmartyResource()
	{
        $this->UnitTestCase('Smarty Resource');
    }
	
	function _getTempDir()
	{
		if (getenv('TMP')) {
			return realpath(getenv('TMP'));
		} elseif (getenv('TMPDIR')) {
			return realpath(getenv('TMPDIR'));
		} elseif (getenv('TEMP')) {
			return realpath(getenv('TEMP'));
		}
		return realpath('/tmp');
	}
	
	function setUp()
	{
		$this->adapter =& new MockAdapter($this->mimetypes);
	}
    
    function testGettingTheResourceOutput()
	{
		$resource =& new SmartyResource($this->adapter, array(
			'url' => '/test',
			'class' => 'SmartyResource',
			'mimetype' => 'text/plain',
			'data' => 'other thing'
		));
		
		$resource->_smarty->template_dir = $this->_getTempDir();
		$resource->_smarty->compile_dir = $this->_getTempDir();
		$resource->_smarty->cache_dir = $this->_getTempDir();
		
		$this->assertEqual($resource->_processWithSmartyModifier('Something with some "{$this->data}" inserted'), 'Something with some "other thing" inserted');
	}
	
}

?>
