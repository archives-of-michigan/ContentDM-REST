<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'response.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';

/**
 * These tests test the putting of resource responses via the request exec method.
 * It uses a mock adapter to retrieve resources.
 * @package Tonic/Tests
 * @version $Revision: 36 $
 */
class TestPutResource extends UnitTestCase
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
        $this->UnitTestCase('Put Resource');
    }
    
    function setUp()
    {
		$this->adapter =& new MockAdapter($this->mimetypes);
		
		$this->request =& new Request();
		$this->request->method = 'put';
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
	
	function testPuttingATonicRepresentationToANewResource()
	{
		$this->request->mimetype = 'application/tonic-resource';
		$this->request->body = "url: /test1\nclass: puttableresource\nmimetype: application/tonic-resource\nmodified: 123456789\ncreated: 111111111\ntitle: Test 1\nrepresentation: /test1.html\nrepresentation: /test1.en\nrepresentation: /test1.en.html\nrepresentation: /test1.json\n\nThis is some body data";
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 201); // created
		$this->assertEqual($this->adapter->resources[$this->request->url], array(
			'url' => '/test1',
			'class' => 'puttableresource',
			'mimetype' => 'application/tonic-resource',
			'modified' => time(),
			'created' => time(),
			'title' => 'Test 1',
			'representation' => array('/test1.html','/test1.en','/test1.en.html','/test1.json'),
			'content' => 'This is some body data'
		));
	}
	
	function testPuttingATonicRepresentationToAnExistingResource()
	{
		$this->adapter->resources[$this->request->url] = array(
			'url' => '/test1',
			'class' => 'PuttableResource',
			'mood' => 'sad'
		);
		$this->request->mimetype = 'application/tonic-resource';
		$this->request->body = "url: /test1\nmood: happy\n";
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 204); // updated
		$this->assertEqual($this->adapter->resources[$this->request->url], array(
			'url' => '/test1',
			'class' => 'puttableresource',
			'mood' => 'happy',
			'mimetype' => 'application/tonic-resource',
			'modified' => time(),
			'created' => time()
		));
	}
	
	function testPuttingAPlainTextRepresentationToANewResource()
	{
		$this->request->mimetype = 'text/plain';
		$this->request->body = 'This is some body data';
		$this->request->{'class'} = 'puttableresource';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 201); // created
		$this->assertEqual($this->adapter->resources[$this->request->url], array(
			'url' => '/test1',
			'class' => 'puttableresource',
			'mimetype' => 'text/plain',
			'modified' => time(),
			'created' => time(),
			'content' => 'This is some body data'
		));
	}
	
	function testPuttingAPlainTextRepresentationToAnExistingResource()
	{
		$this->adapter->resources[$this->request->url] = array(
			'url' => '/test1',
			'class' => 'PuttableResource',
			'content' => 'This is the old body data'
		);
		$this->request->mimetype = 'text/plain';
		$this->request->body = 'This is the new body data';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 204); // updated
		$this->assertEqual($this->adapter->resources[$this->request->url], array(
			'url' => '/test1',
			'class' => 'puttableresource',
			'mimetype' => 'text/plain',
			'modified' => time(),
			'created' => time(),
			'content' => 'This is the new body data'
		));
	}
	
	function testPuttingATonicRepresentationToAnExistingResourceWithAClassName()
	{
		$this->adapter->resources[$this->request->url] = array(
			'url' => '/test1',
			'class' => 'PuttableResource'
		);
		$this->request->mimetype = 'application/tonic-resource';
		$this->request->body = "url: /test1\nclass: DifferentResource";
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 204); // updated
		$this->assertEqual($this->adapter->resources[$this->request->url], array(
			'url' => '/test1',
			'class' => 'DifferentResource',
			'mimetype' => 'application/tonic-resource',
			'modified' => time(),
			'created' => time()
		));
	}
	
	function testGetting412OnIfMatchStarHeaderForNonExistantResource()
	{
		$this->request->ifMatch = array('*');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 412);
	}
	
	function testGettingA411ByPuttingNoRepresentation()
	{
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 411);
	}
	//*/
}

/**
 * @package Tonic/Tests/Mocks
 */
class PuttableResource extends Resource {
	function &put(&$request, &$adapter)
	{
		return $this->_updateResource($request, $adapter);
	}
}

?>
