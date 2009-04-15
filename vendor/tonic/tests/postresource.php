<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'response.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';

/**
 * These tests test the posting of resource responses via the request exec method.
 * It uses a mock adapter to retrieve resources.
 * @package Tonic/Tests
 * @version $Revision: 23 $
 */
class TestPostResource extends UnitTestCase
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
	
	function testPostResource()
	{
        $this->UnitTestCase('Post Resource');
    }
    
    function setUp()
    {
		$this->adapter =& new MockAdapter($this->mimetypes);
		$this->adapter->resources = array(
			'/test1' => array(
				'url' => '/test1',
				'class' => 'PostableResource',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'title' => 'Test 1',
				'representation' => array('/test1.html', '/test1.en', '/test1.en.html', '/test1.json')
		));
		
		$this->request =& new Request();
		$this->request->method = 'post';
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
	
	function testPostingATonicRepresentation()
	{
		$this->request->mimetype = 'application/tonic-resource';
		$this->request->body = "class: PostableResource\nmimetype: application/tonic-resource\nmodified: 123456789\ntitle: Test 1\nrepresentation: /test1.html\nrepresentation: /test1.en\nrepresentation: /test1.en.html\nrepresentation: /test1.json\n\nThis is some body data";
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 201); // created
		$location = $response->headers['Location'];
		$this->assertEqual($location, '/test1/1');
		$this->assertEqual($this->adapter->resources[$location], array(
			'url' => '/test1/1',
			'class' => 'postableresource',
			'mimetype' => 'application/tonic-resource',
			'modified' => time(),
			'created' => time(),
			'title' => 'Test 1',
			'representation' => array('/test1.html','/test1.en','/test1.en.html','/test1.json'),
			'content' => 'This is some body data'
		));
	}
	
	function testPostingATonicMultipleRepresentation()
	{
		$this->request->mimetype = 'application/tonic-resource';
		$this->request->body = "class: PostableResource\nmimetype: application/tonic-resource\nmodified: 123456789\ntitle: Test 1\nrepresentation: /test1.html\nrepresentation: /test1.en\nrepresentation: /test1.en.html\nrepresentation: /test1.json\n\nThis is some body data";
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->headers['Location'], '/test1/1');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->headers['Location'], '/test1/2');
	}
	
	function testPostingAPlainTextRepresentation()
	{
		$this->request->mimetype = 'text/plain';
		$this->request->body = 'This is some body data';
		$class = 'class';
		$this->request->$class = 'PostableResource';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 201); // created
		$location = $response->headers['Location'];
		$this->assertEqual($location, '/test1/1');
		$this->assertEqual($this->adapter->resources[$location], array(
			'mimetype' => 'text/plain',
			'content' => 'This is some body data',
			'class' => 'postableresource',
			'modified' => time(),
			'created' => time(),
			'url' => '/test1/1'
		));
	}
	
	function testGettingA411ByPuttingNoRepresentation()
	{
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 411);
	}
	
	function testPostingToANonExistantResource()
	{
		$this->request->url = '/does-not-exist';
		$class = 'class';
		$this->request->$class = 'PostableResource';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 404);
	}
	//*/
}

/**
 * @package Tonic/Tests/Mocks
 */
class PostableResource extends Resource {
	function &post(&$request)
	{
		return $this->_appendResource($request);
	}
}

?>
