<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'response.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';

/**
 * These tests test the finding of resource via the Resource::find() and Resource::findAll()
 * methods. It uses a mock adapter to retrieve resources.
 * @package Tonic/Tests
 * @version $Revision: 31 $
 */
class TestFindResources extends UnitTestCase
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
	
	function testFindResources()
	{
        $this->UnitTestCase('Find Resources');
    }
    
    function setUp()
    {
		$this->adapter =& new MockAdapter($this->mimetypes);
		$this->adapter->resources = array(
			'/test' => array(
				'url' => '/test',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'title' => 'Test Collection'
			),
			'/test/1' => array(
				'url' => '/test/1',
				'class' => 'resource',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'title' => 'Test Item 1'
			),
			'/test/2' => array(
				'url' => '/test/2',
				'class' => 'resource',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'title' => 'Test Item 2'
			),
			'/test/3' => array(
				'url' => '/test/3',
				'class' => 'resource',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'title' => 'Test Item 3'
			)
		);
    }
	
	function testFindingOneResourceGivenTheExactUrl()
	{
		$foundResource =& Resource::find($this->adapter, '/test');
		$comparisonResource =& new Resource($this->adapter, array(
			'url' => '/test',
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'title' => 'Test Collection'
		));
		$comparisonResource->_exists = TRUE;
		$this->assertIsA($foundResource, 'Resource');
		$this->assertEqual($foundResource, $comparisonResource);
	}
	
	function testFindingOneResourceThatDoesNotExist()
	{
		$foundResource =& Resource::find($this->adapter, '/does-not-exist');
		$this->assertFalse($foundResource);
	}
	
	function testFindingOneResourceGivenTheExactUrlForcingASpecificResourceClass()
	{
		$options = array(
			TONIC_FIND_FORCE_METADATA => array(
				'class' => 'FindChildResource'
		));
		$foundResource =& Resource::find($this->adapter, '/test', $options);
		$this->assertIsA($foundResource, 'FindChildResource');
		$class = 'class';
		$this->assertEqual($foundResource->$class, 'findchildresource');
	}
	
	function testFindingOneResourceGivenTheExactUrlForcingSpecificMetadata()
	{
		$options = array(
			TONIC_FIND_FORCE_METADATA => array(
				'title' => 'My Title'
		));
		$foundResource =& Resource::find($this->adapter, '/test', $options);
		$this->assertEqual($foundResource->title, 'My Title');
	}
	
	function testFindingOneResourceGivenTheExactUrlUsingDefaultMetadata()
	{
		$options = array(
			TONIC_FIND_DEFAULT_METADATA => array(
				'title' => 'My Title',
				'test' => 'default'
		));
		$foundResource =& Resource::find($this->adapter, '/test', $options);
		$this->assertEqual($foundResource->title, 'Test Collection');
		$this->assertEqual($foundResource->test, 'default');
	}
	
	function testFindingAllResourcesMatchingAUrl()
	{
		$foundResources =& Resource::findAll($this->adapter, '/test/');
		$this->assertEqual(count($foundResources), 3);
	}
	
	function testFindingAllResourcesMatchingAUrlNotEndingInASlash()
	{
		$foundResources =& Resource::findAll($this->adapter, '/test');
		$this->assertEqual(count($foundResources), 4);
	}
	
	function testFindingAllResourcesMatchingAUrlForcingASpecificResourceClass()
	{
		$options = array(
			TONIC_FIND_FORCE_METADATA => array(
				'class' => 'FindChildResource'
		));
		$foundResources =& Resource::findAll($this->adapter, '/test/', $options);
		$class = 'class';
		foreach ($foundResources as $foundResource) {
			$this->assertEqual($foundResource->$class, 'findchildresource');
		}
	}
	
	function testFindingAllResourcesMatchingAUrlForcingSpecificMetadata()
	{
		$options = array(
			TONIC_FIND_FORCE_METADATA => array(
				'title' => 'My Title'
		));
		$foundResources =& Resource::findAll($this->adapter, '/test/', $options);
		foreach ($foundResources as $foundResource) {
			$this->assertEqual($foundResource->title, 'My Title');
		}
	}
	
	function testFindingAllResourcesMatchingAUrlUsingDefaultMetadata()
	{
		$options = array(
			TONIC_FIND_DEFAULT_METADATA => array(
				'title' => 'My Title',
				'test' => 'default'
		));
		$foundResources =& Resource::findAll($this->adapter, '/test/', $options);
		foreach ($foundResources as $foundResource) {
			$this->assertNotEqual($foundResource->title, 'My Title');
			$this->assertEqual($foundResource->test, 'default');
		}
	}
}

// test Resource child classes
require_once 'lib/resource.php';

/**
 * @package Tonic/Tests/Mocks
 */
class FindChildResource extends Resource {}

?>
