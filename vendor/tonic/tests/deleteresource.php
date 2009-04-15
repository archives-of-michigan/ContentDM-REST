<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'response.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';

/**
 * These tests test the deleting of resource responses via the request exec method.
 * It uses a mock adapter to retrieve resources.
 * @package Tonic/Tests
 * @version $Revision: 23 $
 */
class TestDeleteResource extends UnitTestCase
{
	
	var $adapter, $request;
	
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
	
	function testPutResource()
	{
        $this->UnitTestCase('Delete Resource');
    }
    
    function setUp()
    {
		$this->adapter =& new MockAdapter($this->mimetypes);
		$this->adapter->resources = array(
			'/test1' => array(
				'url' => '/test1',
				'class' => 'DeletableResource',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'title' => 'Test 1',
				'representation' => array('/test1.html', '/test1.en', '/test1.en.html', '/test1.json')
		));
		
		$this->request =& new Request();
		$this->request->method = 'delete';
		$this->request->url = '/test1';
		$this->request->mimetype = NULL;
		$this->request->body = NULL;
		$this->request->accept = array();
		$this->request->language = array();
		$this->request->encoding = array();
		$this->request->ifNoneMatch = array();
		$this->request->ifMatch = array();
		$this->request->ifModifiedSince = 0;
		$this->request->ifUnmodifiedSince = 0;
    }
	
	function testDeletingAResource()
	{
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 204); // no content
		$this->assertFalse(isset($this->adapter->resources[$this->request->url]));
	}
	
	function testDeletingANonExistantResource()
	{
		$this->request->url = '/does-not-exist';
		$class = 'class';
		$this->request->$class = 'DeletableResource';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 404);
	}
	
}

/**
 * @package Tonic/Tests/Mocks
 */
class DeletableResource extends Resource {
	function &delete(&$request)
	{
		$response =& $this->_deleteResource($request);
		return $response;
	}
}

?>
