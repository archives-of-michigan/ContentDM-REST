<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'response.php';

/**
 * @package Tonic/Tests
 */
class TestResource extends UnitTestCase
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
	
	function testResource() {
        $this->UnitTestCase('Tonic Resource');
    }
	
	function setUp()
	{
		$this->request =& new Request();
		$this->request->method = 'get';
		$this->request->url = '/test.html';
		$this->request->mimetype = NULL;
		$this->request->body = NULL;
		$this->request->accept = array();
		$this->request->language = array();
		$this->request->encoding = array();
		$this->request->ifNoneMatch = array();
		$this->request->ifMatch = array();
		$this->request->ifModifiedSince = 0;
		$this->request->ifUnmodifiedSince = 0;
		$this->request->extensions = array('html');
		
		$this->adapter =& new MockAdapter($this->mimetypes);
		$this->adapter->resources = array(
			'/shell.html' => array(
				'url' => '/shell',
				'class' => 'Resource',
				'mimetype' => 'text/html',
				'modified' => '123456789',
				'created' => '111111111',
				'content' => 'test'
			),
			'/shell-with-no-content.html' => array(
				'url' => '/shell-with-no-content',
				'class' => 'Resource',
				'mimetype' => 'text/html',
				'modified' => '123456789',
				'created' => '111111111'
			)
		);
	}
    
	function testSettingAPrivatePieceOfMetadata()
	{
		$data = array(
			'url' => '/test'
		);
		$resource =& new Resource($this->adapter, $data);
		$this->assertFalse($resource->set('_private', 'private'));
		$this->assertFalse(isset($resource->_private));
	}
	
	function testTonicFormatOfAResourceWithAnotherResourceAsAMember()
	{
		$data = array(
			'url' => '/nested'
		);
		$nestedResource =& new Resource($this->adapter, $data);
		$data = array(
			'url' => '/test',
			'nested' => $nestedResource
		);
		$resource =& new Resource($this->adapter, $data);
		$tonicFormat = Resource::encodeResourceIntoTonicFormat($resource);
		$this->assertEqual($tonicFormat, 'url: /test
nested: /nested
class: resource
mimetype: application/tonic-resource
created: '.time().'
modified: '.time().'
');
	}
	
	function testRedirectResponseWithRealRepresentation()
	{
		$this->request->url = '/test';
		$this->request->fullUrl = '/test';
		$data = array(
			'url' => '/test',
			'representation' => '/shell.html'
		);
		$resource =& new Resource($this->adapter, $data);
		$resource->_exists = TRUE;
		$response =& $resource->get($this->request);
		$this->assertEqual($response->statusCode, 302);
		$this->assertEqual($response->headers['Location'], '/test.html');
	}
	
	function testResourceRepresentationLoadingWithRealRepresentation()
	{	
		$data = array(
			'url' => '/test',
			'representation' => '/shell.html'
		);
		$resource =& new Resource($this->adapter, $data);
		$resource->_exists = TRUE;
		$response =& $resource->get($this->request);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $representation->get($this->request);
		$this->assertEqual($response2->statusCode, 200);
		$this->assertEqual($response2->body, 'test');
	}
	
	function testResourceRepresentationLoadingWithNoRepresentation()
	{
		$data = array(
			'url' => '/test',
			'representation' => '/noshell.html'
		);
		$resource =& new Resource($this->adapter, $data);
		$resource->_exists = TRUE;
		$response =& $resource->get($this->request);
		$representation =& $resource->loadRepresentation($this->adapter);
		$this->assertFalse($representation);
	}
	
	function testResourceRepresentationLoadingWithBadRepresentation()
	{
		$data = array(
			'url' => '/test',
			'representation' => '/shell-with-no-content.html'
		);
		$resource =& new Resource($this->adapter, $data);
		$resource->_exists = TRUE;
		$response =& $resource->get($this->request);
		$this->assertEqual($response->statusCode, 200);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $representation->get($this->request);
		$this->assertEqual($response2->statusCode, 406);
	}
	//*/
}

?>
