<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'response.php';
require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';

/**
 * These tests test the getting of resource responses via the request exec method.
 * It uses a mock adapter to retrieve resources.
 * @package Tonic/Tests
 * @version $Revision: 23 $
 */
class TestGetResource extends UnitTestCase
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
	
    function testGetResource()
	{
        $this->UnitTestCase('Get Resource');
    }
    
    function setUp()
    {
		$this->adapter =& new MockAdapter($this->mimetypes);
		$this->adapter->resources = array(
			'/test1' => array(
				'url' => '/test1',
				'class' => 'Resource',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'created' => '111111111',
				'title' => 'Test 1',
				'representation' => array('/test1.html', '/test1.en', '/test1.en.html', '/test1.json')
			),
			'/test1.html' => array(
				'url' => '/test1.html',
				'class' => 'Resource',
				'mimetype' => 'text/html',
				'modified' => '123456789',
				'created' => '111111111',
				'content' => 'HTML'
			),
			'/test1.en' => array(
				'url' => '/test1.en',
				'class' => 'Resource',
				'mimetype' => 'text/plain',
				'modified' => '123456789',
				'created' => '111111111',
				'content' => 'English content'
			),
			'/test1.en.html' => array(
				'url' => '/test1.en.html',
				'class' => 'Resource',
				'mimetype' => 'text/html',
				'modified' => '123456789',
				'created' => '111111111',
				'content' => 'English HTML content'
			),
			'/test1.json' => array(
				'url' => '/test1.json',
				'class' => 'Resource',
				'mimetype' => 'application/json',
				'modified' => '123456789',
				'created' => '111111111',
				'content' => 'Some JSON'
			),
			'/different-class' => array(
				'url' => '/different-class',
				'class' => 'GetChildResource',
				'content' => 'This is of type GetChildResource',
				'cacheControl' => 12345
			),
			'/just-content.txt' => array(
				'content' => 'This just has content, no metadata'
			),
			'/secured-by-basic' => array(
				'url' => '/secured-by-basic',
				'class' => 'BasicSecureResource',
				'content' => 'This is a HTTP Basic secure resource'
			),
			'/secured-by-cookie' => array(
				'url' => '/secured-by-cookie',
				'class' => 'CookieSecureResource',
				'content' => 'This is a HTTP Cookie secure resource'
			),
			'/secured-by-digest' => array(
				'url' => '/secured-by-digest',
				'class' => 'DigestSecureResource',
				'content' => 'This is a HTTP Digest secure resource'
			),
			'/representation-only.txt' => array(
				'url' => '/representation-only.txt',
				'class' => 'Resource',
				'mimetype' => 'text/plain',
				'content' => 'This is a plain text representation that has no base resource'
			),
			'/representation-only-with-no-data.txt' => array(
				'url' => '/representation-only.txt',
				'content' => 'This is a plain text representation that has no base resource'
			),
			'/resource-with-type-with-representations.txt' => array(
				'url' => '/resource-with-type-with-representations.txt',
				'representation' => '/test.html',
				'content' => 'This is a plain text representation that has no base resource'
			)
		);
		
		$this->request =& new Request();
		$this->request->method = 'get';
		$this->request->url = '/test1';
		$this->request->fullUrl = '/test1';
		$this->request->accept = array();
		$this->request->language = array();
		$this->request->encoding = array();
		$this->request->ifNoneMatch = array();
		$this->request->ifMatch = array();
		$this->request->ifModifiedSince = 0;
		$this->request->ifUnmodifiedSince = 0;
    }
    
    function testGettingAResourcesDefaultRepresentation()
	{
		$this->request->url = '/test1';
		$this->request->accept = array('application/tonic-resource');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
		$this->assertEqual($response->body, "url: /test1\nclass: resource\nmimetype: application/tonic-resource\nmodified: 123456789\ncreated: 111111111\ntitle: Test 1\nrepresentation: /test1.html\nrepresentation: /test1.en\nrepresentation: /test1.en.html\nrepresentation: /test1.json\n");
	}
	
	function testGettingAResourcesHtmlRepresentationByTypeExtension()
	{
		$this->request->url = '/test1.html';
		$this->request->accept = array('text/plain');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $this->request->exec($this->adapter, $representation);
		$this->assertEqual($response2->statusCode, 200);
		$this->assertEqual($response2->body, 'HTML');
	}
	
	function testGettingAResourcesHtmlRepresentationByLanguageExtension()
	{
		$this->request->url = '/test1.en';
		$this->request->accept = array('text/plain');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $this->request->exec($this->adapter, $representation);
		$this->assertEqual($response2->statusCode, 200);
		$this->assertEqual($response2->body, "English content");
	}
	
	function testGettingAResourcesHtmlRepresentationByTypeAndLanguageExtension()
	{
		$this->request->url = '/test1.en.html';
		$this->request->accept = array('text/plain');
		$this->request->language = array('fr');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $this->request->exec($this->adapter, $representation);
		$this->assertEqual($response2->statusCode, 200);
		$this->assertEqual($response2->body, "English HTML content");
	}
	
	function testGettingAResourcesHtmlRepresentationByAcceptHeader()
	{
		$this->request->accept = array('text/html');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 302);
		$this->assertEqual($response->headers['Location'], '/test1.html');
	}
	
	function testGettingAResourcesHtmlRepresentationByLanguageHeader()
	{
		$this->request->language = array('en');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 302);
		$this->assertEqual($response->headers['Location'], '/test1.en');
	}
	
	function testGettingAResourcesDefaultRepresentationOnUnrecognisedAcceptHeader()
	{
		$this->request->accept = array('type/unknown');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
		$this->assertEqual($response->body, "url: /test1\nclass: resource\nmimetype: application/tonic-resource\nmodified: 123456789\ncreated: 111111111\ntitle: Test 1\nrepresentation: /test1.html\nrepresentation: /test1.en\nrepresentation: /test1.en.html\nrepresentation: /test1.json\n");
	}
	
	function testGettingANotAcceptableOnUnrecognisedExtension()
	{
		$this->request->url = '/test1.woot';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 406);
		$this->assertEqual($response->body, NULL);
	}
	
	function testGettingARepresentationThatHasNoResource()
	{
		$this->request->url = '/representation-only.txt';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
		$this->assertEqual($response->body, 'This is a plain text representation that has no base resource');
	}
	
	function testGettingARepresentationThatHasNoResourceAndNoMetadata()
	{
		$this->request->url = '/representation-only-with-no-data.txt';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
		$this->assertEqual($response->body, 'This is a plain text representation that has no base resource');
	}
	
	function testGettingANonExistantResource()
	{
		$this->request->url = '/test2';
		$this->request->accept = array('text/html');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 404);
		$this->assertEqual($response->body, NULL);
	}
	
	function testGettingANonExistantResourceAndRepresentation()
	{
		$this->request->url = '/test2.woot';
		$this->request->accept = array('text/html');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 404);
		$this->assertEqual($response->body, NULL);
	}
	
	function testGettingExecutingANonExistantMethodOnAResource()
	{
		$this->request->method = 'WOOT';
		$this->request->accept = array('text/html');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 405);
		$this->assertEqual($response->body, NULL);
	}
	
	function testGettingAResourceAsAChildClassViaItsMetadata()
	{
		$resource =& Resource::find($this->adapter, '/different-class');
		$class = 'class';
		$this->assertEqual($resource->$class, 'getchildresource');
	}
	
	function testGettingAResourceThatOnlyHasContent()
	{
		$resource =& Resource::find($this->adapter, '/just-content.txt');
		$this->assertEqual($resource->content, 'This just has content, no metadata');
	}
	
	function testGettingCorrectEntityTagHeader()
	{
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->headers['Etag'], '"'.md5('123456789').'"');
	}
	
	function testGettingCorrectLastModifiedHeader()
	{
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->headers['Last-Modified'], date('r', 123456789));
	}
	
	function testGetting304OnCorrectIfNoneMatchHeader()
	{
		$this->request->ifNoneMatch = array(md5('123456789'));
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 304);
	}
	
	function testGetting304OnStarIfNoneMatchHeader()
	{
		$this->request->ifNoneMatch = array('*');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 304);
	}
	
	function testGetting200OnIncorrectIfNoneMatchHeader()
	{
		$this->request->ifNoneMatch = array(md5('123456788'));
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	function testGetting200OnCorrectIfMatchHeader()
	{
		$this->request->ifMatch = array(md5('123456789'));
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	function testGetting200OnStarIfMatchHeader()
	{
		$this->request->ifMatch = array('*');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	function testGetting412OnIncorrectIfMatchHeader()
	{
		$this->request->ifMatch = array(md5('123456788'));
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 412);
	}
	
	function testGetting304OnCorrectIfModifiedSince()
	{
		$this->request->ifModifiedSince = 123456790;
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 304);
	}
	
	function testGetting200OnIncorrectIfModifiedSince()
	{
		$this->request->ifModifiedSince = 123456788;
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	function testGetting200OnCorrectIfUnmodifiedSince()
	{
		$this->request->ifUnmodifiedSince = 123456790;
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	function testGetting412OnIncorrectIfUnmodifiedSince()
	{
		$this->request->ifUnmodifiedSince = 123456788;
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 412);
	}
	
	function testGettingADefaultCacheControlHeaderInTheResponse()
	{
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->headers['Cache-Control'], 'max-age=86400, must-revalidate');
	}
	
	function testGettingACustomCacheControlHeaderInTheResponse()
	{
		$this->request->url = '/different-class';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->headers['Cache-Control'], 'max-age=12345, must-revalidate');
	}
	
	function testGettingAGZippedResponseGivenACorrectEncodingHeader()
	{
		$this->request->url = '/test1.html';
		$this->request->encoding = array('gzip', 'deflate');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $this->request->exec($this->adapter, $representation);
		$this->assertEqual($response2->headers['Content-Encoding'], 'gzip');
		$this->assertEqual($response2->body, gzencode('HTML'));
	}
	
	function testGettingADeflatedResponseGivenACorrectEncodingHeader()
	{
		$this->request->url = '/test1.html';
		$this->request->encoding = array('deflate', 'gzip');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $this->request->exec($this->adapter, $representation);
		$this->assertEqual($response2->headers['Content-Encoding'], 'deflate');
		$this->assertEqual($response2->body, gzdeflate('HTML'));
	}
	
	function testGettingACompressedResponseGivenACorrectEncodingHeader()
	{
		$this->request->url = '/test1.html';
		$this->request->encoding = array('compress', 'gzip');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $this->request->exec($this->adapter, $representation);
		$this->assertEqual($response2->headers['Content-Encoding'], 'compress');
		$this->assertEqual($response2->body, gzcompress('HTML'));
	}
	
	function testGettingAnIdentityResponseGivenACorrectEncodingHeader()
	{
		$this->request->url = '/test1.html';
		$this->request->encoding = array('identity', 'gzip');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$representation =& $resource->loadRepresentation($this->adapter);
		$response2 =& $this->request->exec($this->adapter, $representation);
		$this->assertFalse(isset($response2->headers['Content-Encoding']));
		$this->assertEqual($response2->body, 'HTML');
	}
	
	function testGettingAHTTPBasicAuthHeader()
	{
		$this->request->url = '/secured-by-basic';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 401);
		$realm = 'Tonic';
		if (ini_get('safe_mode')) {
			$realm .= '-'.getmyuid();
		}
		$this->assertEqual($response->headers['WWW-Authenticate'], 'Basic realm="'.$realm.'"');
	}
	
	function testGettingAccessToSecureResourceWithCorrectBasicAuthDetails()
	{
		$this->request->url = '/secured-by-basic';
		$this->request->basicAuth = array(
			'username' => 'root',
			'password' => 'xyzzy'
		);
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	function testGettingAHTTPDigestAuthHeader()
	{
		$this->request->url = '/secured-by-digest';
		$realm = Resource::getRealm('Tonic');
		$nonce = Resource::getNonce('myPrivateKey', 5, '1.2.3.4');
		$opaque = md5('anOpaqueValue');
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 401);
		$this->assertEqual($response->headers['WWW-Authenticate'], 'Digest realm="'.$realm.'", domain="'.$this->request->url.'", qop=auth, algorithm=MD5, nonce="'.$nonce.'", opaque="'.$opaque.'"');
	}
	
	function testGettingAccessToSecureResourceWithCorrectDigestAuthDetails()
	{
		$this->request->url = '/secured-by-digest';
		$realm = Resource::getRealm('Tonic');
		$nonce = Resource::getNonce('myPrivateKey', 5, '1.2.3.4');
		$a1 = md5('root'.':'.$realm.':'.'xyzzy');
		$a2 = md5($this->request->method.':'.$this->request->url);
		$this->request->digestAuth = array(
			'username' => 'root',
			'uri' => $this->request->url,
			'realm' => $realm,
			'nonce' => $nonce,
			'opaque' => 'anOpaqueValue',
			'response' => md5($a1.':'.$nonce.':'.$a2)
		);
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	function testGettingAHTTPCookieAuthHeader()
	{
		$this->request->url = '/secured-by-cookie';
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 401);
	}
	
	function testGettingAccessToSecureResourceWithCorrectCookieAuthDetails()
	{
		$this->request->url = '/secured-by-cookie';
		$this->request->cookieAuth = array(
			'username' => 'root',
			'hash' => md5('root'.Resource::getNonce('myPrivateKey', 5, '1.2.3.4'))
		);
		$resource =& $this->request->load($this->adapter);
		$response =& $this->request->exec($this->adapter, $resource);
		$this->assertEqual($response->statusCode, 200);
	}
	
	//*/
}

// test Resource child classes
require_once 'lib/resource.php';

/**
 * @package Tonic/Tests/Mocks
 */
class GetChildResource extends Resource {}

/**
 * @package Tonic/Tests/Mocks
 */
class SecureResource extends Resource {
	var $username = 'root';
	var $password = 'xyzzy';
}

/**
 * @package Tonic/Tests/Mocks
 */
class BasicSecureResource extends SecureResource {
	function &get(&$request)
	{
		$config['realm'] = 'Tonic';
		
		if (!$this->_authorisedByBasicAuth($request, $config, $this->username, $this->password)) {
			$response =& new Response(401);
			$response->sendBasicAuthHeader($config, $this);
			return $response;
		}
		return parent::get($request);
	}
}

/**
 * @package Tonic/Tests/Mocks
 */
class DigestSecureResource extends SecureResource {
	function &get(&$request)
	{
		$config['realm'] = 'Tonic';
		$config['privateKey'] = 'myPrivateKey';
		$config['life'] = 5;
		$config['clientAddress'] = '1.2.3.4';
		$config['opaque'] = 'anOpaqueValue';
		
		if (!$this->_authorisedByDigestAuth($request, $config, $this->username, $this->password)) {
			$response =& new Response(401);
			$response->sendDigestAuthHeader($config, $this);
			return $response;
		}
		return parent::get($request);
	}
}

/**
 * @package Tonic/Tests/Mocks
 */
class CookieSecureResource extends SecureResource {
	var $_config;
	
	function &get(&$request)
	{
		$this->_config['realm'] = 'Tonic';
		$this->_config['privateKey'] = 'myPrivateKey';
		$this->_config['life'] = 5;
		$this->_config['clientAddress'] = '1.2.3.4';
		
		if (!$this->_authorisedByCookieAuth($request, $this->_config, $this->username, $this->password)) {
			$response =& new Response(401);
			return $response;
		} else { // output login form
			return parent::get($request);
		}
	}
	
	function &post(&$request)
	{
		if (
			isset($_POST['username']) && $_POST['username'] == $this->username &&
			isset($_POST['password']) && $_POST['password'] == $this->password
		) {
			$response->sendAuthCookie($this->_config, $this, $request->cookieAuth['username']);
			$response =& new Response(302, NULL, array('Location' => $this->url)); // success, redirect to GET request on this resource
			return $response;
		}
		return parent::get($request);
	}
}

?>
