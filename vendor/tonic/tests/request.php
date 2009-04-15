<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'request.php';

/**
 * @package Tonic/Tests
 */
class TestRequest extends UnitTestCase
{
	
	function testRequest() {
        $this->UnitTestCase('HTTP Request');
    }
	
	function setUp()
	{
		unset($_SERVER['REQUEST_METHOD']);
		unset($_SERVER['REQUEST_URI']);
		unset($_SERVER['PHP_SELF']);
		unset($_SERVER['HTTP_ACCEPT']);
		unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		unset($_SERVER['CONTENT_TYPE']);
		unset($_SERVER['CONTENT_LENGTH']);
		unset($_SERVER['HTTP_IF_MATCH']);
		unset($_SERVER['HTTP_IF_NONE_MATCH']);
		unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
		unset($_SERVER['HTTP_IF_UNMODIFIED_SINCE']);
		unset($_SERVER['PHP_AUTH_USER']);
		unset($_SERVER['PHP_AUTH_PW']);
		unset($_SERVER['Authorization']);
		unset($_COOKIE['tonic']);
	}
    
	function testGettingHTTPMethod()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$request =& new Request();
		$this->assertEqual($request->method, 'get');
	}
	
	function testGettingURL()
	{
		$_SERVER['PHP_SELF'] = '/dispatch.php';
		$_SERVER['REQUEST_URI'] = '/test1';
		$request =& new Request();
		$this->assertEqual($request->url, '/test1');
	}
	
	function testGettingRootURL()
	{
		$_SERVER['PHP_SELF'] = '/dispatch.php';
		$_SERVER['REQUEST_URI'] = '/';
		$request =& new Request();
		$this->assertEqual($request->url, '/');
	}
	
	function testGettingURLFromNonRootOfUrlSpace()
	{
		$_SERVER['PHP_SELF'] = '/woo/yay/dispatch.php';
		$_SERVER['REQUEST_URI'] = '/woo/yay/test1';
		$request =& new Request();
		$this->assertEqual($request->url, '/test1');
	}
	
	function testSingleAcceptHeader()
	{
		$_SERVER['HTTP_ACCEPT'] = 'text/html';
		$request =& new Request();
		$this->assertEqual($request->accept, array('text/html'));
	}
	
	function testNoAcceptHeader()
	{
		$request =& new Request();
		$this->assertEqual($request->accept, array('text/html'));
	}
	
	function testComplexAcceptHeader()
	{
		$_SERVER['HTTP_ACCEPT'] = 'text/xml,text/html;q=0.9,text/plain;q=0.8';
		$request =& new Request();
		$this->assertEqual($request->accept, array(
			'text/xml', 'text/html', 'text/plain'
		));
	}
	
	function testBackwardsComplexAcceptHeader()
	{
		$_SERVER['HTTP_ACCEPT'] = 'text/xml;q=0.8,text/html;q=0.9,text/plain';
		$request =& new Request();
		$this->assertEqual($request->accept, array(
			'text/plain', 'text/html', 'text/xml'
		));
	}
	
	function testFirefoxAcceptHeader()
	{
		$_SERVER['HTTP_ACCEPT'] = 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
		$request =& new Request();
		$this->assertEqual($request->accept, array(
			'text/xml', 'application/xml', 'application/xhtml+xml', 'image/png', 'text/html', 'text/plain', 'text/html'
		));
	}
	
	function testIEAcceptHeader()
	{
		$_SERVER['HTTP_ACCEPT'] = '*/*';
		$request =& new Request();
		$this->assertEqual($request->accept, array(
			'text/html'
		));
	}
	
	function testSingleLanguageHeader()
	{
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
		$request =& new Request();
		$this->assertEqual($request->language, array('en'));
	}
	
	function testComplexLanguageHeader()
	{
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en;q=0.5,fr-fr;q=0.3,en-gb';
		$request =& new Request();
		$this->assertEqual($request->language, array('en-gb', 'en', 'fr-fr', 4 => 'fr'));
	}
	
	function testSingleEncodingHeader()
	{
		$_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip';
		$request =& new Request();
		$this->assertEqual($request->encoding, array('gzip'));
	}
	
	function testMultipleEncodingHeader()
	{
		$_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip, deflate';
		$request =& new Request();
		$this->assertEqual($request->encoding, array('gzip', 'deflate'));
	}
	
	function testPostRequestBody()
	{
		global $HTTP_RAW_POST_DATA;
		$HTTP_RAW_POST_DATA = '1234567890';
		$_SERVER['CONTENT_LENGTH'] = strlen($HTTP_RAW_POST_DATA);
		$request =& new Request();
		$this->assertEqual($request->body, '1234567890');
	}
	
	function testGettingNonPostRequestBodyFromRequest()
	{
		// can't test this!
	}
	
	function testRequestBodyMimetype()
	{
		$_SERVER['CONTENT_TYPE'] = 'text/html';
		$request =& new Request();
		$this->assertEqual($request->mimetype, 'text/html');
	}
	
	function testRequestBodyMimetypeWhenThereIsNone()
	{
		$request =& new Request();
		$this->assertEqual($request->mimetype, NULL);
	}
	
	function testSingleRequestIfMatch()
	{
		$_SERVER['HTTP_IF_MATCH'] = '"123456789"';
		$request =& new Request();
		$this->assertEqual($request->ifMatch, array('123456789'));
	}
	
	function testSingleRequestIfNoneMatch()
	{
		$_SERVER['HTTP_IF_NONE_MATCH'] = '"123456789"';
		$request =& new Request();
		$this->assertEqual($request->ifNoneMatch, array('123456789'));
	}
	
	function testMultipleRequestIfNoneMatch()
	{
		$_SERVER['HTTP_IF_NONE_MATCH'] = '"123456789", "987654321"';
		$request =& new Request();
		$this->assertEqual($request->ifNoneMatch, array('123456789', '987654321'));
	}
	
	function testNoRequestIfNoneMatch()
	{
		unset($_SERVER['HTTP_IF_NONE_MATCH']);
		$request =& new Request();
		$this->assertEqual($request->ifNoneMatch, array());
	}
	
	function testIfModifiedSinceHeader()
	{
		$_SERVER['HTTP_IF_MODIFIED_SINCE'] = date('r');
		$request =& new Request();
		$this->assertEqual($request->ifModifiedSince, time());
	}
	
	function testNoIfModifiedSinceHeader()
	{
		unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
		$request =& new Request();
		$this->assertEqual($request->ifModifiedSince, 0);
	}
	
	function testIfUnmodifiedSinceHeader()
	{
		$_SERVER['HTTP_IF_UNMODIFIED_SINCE'] = date('r');
		$request =& new Request();
		$this->assertEqual($request->ifUnmodifiedSince, time());
	}
	
	function testBasicAuthHeader()
	{
		$_SERVER['PHP_AUTH_USER'] = 'username';
		$_SERVER['PHP_AUTH_PW'] = 'password';
		$request =& new Request();
		$this->assertEqual($request->basicAuth, array('username' => 'username', 'password' => 'password'));
	}
	
	function testDigestAuthHeader()
	{
		$_SERVER['Authorization'] = 'Digest username="username", realm="Realm", nonce="2da1a3906a0c6878c5918d3b487f5b44", uri="/woo", algorithm=MD5, response="dc6a12bb8cd65491a796276d63be07dd", opaque="94619f8a70068b2591c2eed622525b0e", qop=auth, nc=00000001, cnonce="b2935a731ce248e3"';
		$request =& new Request();
		$this->assertEqual($request->digestAuth, array(
			'username' => 'username',
			'nonce' => '2da1a3906a0c6878c5918d3b487f5b44',
			'response' => 'dc6a12bb8cd65491a796276d63be07dd',
			'opaque' => '94619f8a70068b2591c2eed622525b0e',
			'uri' => '/woo',
			'qop' => 'auth',
			'nc' => '00000001',
			'cnonce' => 'b2935a731ce248e3'
		));
	}
	
	function testCookieAuthHeader()
	{
		$_COOKIE['tonic'] = 'username:2da1a3906a0c6878c5918d3b487f5b44';
		$request =& new Request();
		$this->assertEqual($request->cookieAuth, array(
			'username' => 'username',
			'hash' => '2da1a3906a0c6878c5918d3b487f5b44'
		));
	}
}
?>
