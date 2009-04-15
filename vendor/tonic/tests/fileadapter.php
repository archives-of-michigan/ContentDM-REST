<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';
require_once 'adapters'.DIRECTORY_SEPARATOR.'fileadapter.php';

/**
 * These tests test the deleting of resource responses via the request exec method.
 * It uses a mock adapter to retrieve resources.
 * @package Tonic/Tests
 * @version $Revision: 28 $
 * @author Paul James
 */
class TestFileAdapter extends UnitTestCase
{
	
	var $adapter, $tmp;
	
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
	
	function testFileAdapter()
	{
        $this->UnitTestCase('File Adapter');
		$this->tmp = $this->_getTempDir();
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
	
	function _removeDirectory($dir)
	{
		$files = glob($dir.DIRECTORY_SEPARATOR.'*');
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->_removeDirectory($file);
			} else {
				@unlink($file);
			}
		}
		@rmdir($dir);
	}
    
    function setUp()
    {
		$this->adapter =& new FileAdapter($this->mimetypes, $this->tmp.DIRECTORY_SEPARATOR.'tonic-test');
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test-normal', 'w');
		fwrite($fp, "url: /test-normal\nclass: resource\nmimetype: application/tonic-resource\nmodified: 100000001\ntitle: Test file\n\nThis is the resource body");
		fclose($fp);
		@mkdir($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test');
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'default.html', 'w');
		fwrite($fp, "url: /test\nclass: resource\nmimetype: application/tonic-resource\nmodified: 123456789\ncreated: 111111111\ntitle: Test\n\nThis is the resource body");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'default.html', 'w');
		fwrite($fp, "url: /\nclass: resource\nmimetype: application/tonic-resource\ntitle: Root resource\n\nThis is the magic root resource");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'1', 'w');
		fwrite($fp, "url: /test/1\nclass: resource\nmimetype: application/tonic-resource\nmodified: 100000001\ntitle: Test 1\n\nThis is the resource body");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'2', 'w');
		fwrite($fp, "url: /test/2\nclass: resource\nmimetype: application/tonic-resource\nmodified: 100000000\ntitle: Test 2\n\nThis is the resource body 2");
		fclose($fp);
		@mkdir($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'another');
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'another'.DIRECTORY_SEPARATOR.'default.html', 'w');
		fwrite($fp, "url: /test/another\nclass: resource\nmimetype: application/tonic-resource\nmodified: 100000002\ntitle: Test\n\nThis is the resource body");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'another'.DIRECTORY_SEPARATOR.'1', 'w');
		fwrite($fp, "url: /test/another/1\nclass: resource\nmimetype: application/tonic-resource\nmodified: 123456789\ntitle: Another test 1\n\nThis is another body");
		fclose($fp);
		@mkdir($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'offset');
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'offset'.DIRECTORY_SEPARATOR.'1', 'w');
		fwrite($fp, "url: /offset/1\n\nOffset 1");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'offset'.DIRECTORY_SEPARATOR.'2', 'w');
		fwrite($fp, "url: /offset/2\n\nOffset 2");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'offset'.DIRECTORY_SEPARATOR.'3', 'w');
		fwrite($fp, "url: /offset/3\n\nOffset 3");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'offset'.DIRECTORY_SEPARATOR.'4', 'w');
		fwrite($fp, "url: /offset/4\n\nOffset 4");
		fclose($fp);
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'no-metadata', 'w');
		fwrite($fp, "This contains nothing but content");
		fclose($fp);
	}
	
	function tearDown()
	{
		$this->_removeDirectory($this->tmp.DIRECTORY_SEPARATOR.'tonic-test');
	}
	
	function testAdapterPathIsCreatedIfItDoesNotExist()
	{
		$this->assertTrue(is_dir($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'));
	}
	
	function testGetARegularFile()
	{
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $this->adapter->select('/test-normal', $options);
		$this->assertEqual($selectedResourceData['/test-normal']['url'], '/test-normal');
	}
	
	function testGetAFileWithTheSameNameAsADirectory()
	{
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $this->adapter->select('/test', $options);
		$comparisonResourceData = array(
			'/test' => array(
				'url' => '/test',
				'class' => 'resource',
				'mimetype' => 'application/tonic-resource',
				'modified' => '123456789',
				'created' => '111111111',
				'title' => 'Test',
				'content' => 'This is the resource body'
		));
		$this->assertEqual($selectedResourceData, $comparisonResourceData);
	}
	
	function testGetTheResourceWithTheRootUrl()
	{
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $this->adapter->select('/', $options);
		$this->assertEqual($selectedResourceData['/']['url'], '/');
	}
	
	function testGetTheResourceWithTrailingSlash()
	{
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual($selectedResourceData['/test']['url'], '/test');
	}
	
	function testGetACollectionOfResourcesMatchingAUrl()
	{
		$selectedResourceData =& $this->adapter->select('/test/');
		$this->assertEqual(count($selectedResourceData), 3);
	}
	
	function testGetACollectionOfResourcesMatchingAUrlWithDeeperPaths()
	{
		$selectedResourceData =& $this->adapter->select('/test/another');
		$this->assertEqual(count($selectedResourceData), 1);
	}
	
	function testGetACollectionOfResourcesMatchingAUrlLimitedByMetadata()
	{
		$options[TONIC_FIND_BY_METADATA] = array(
			'title' => 'Test 2'
		);
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(count($selectedResourceData), 1);
		$this->assertEqual($selectedResourceData['/test/2']['title'], 'Test 2');
	}
	
	function testGetACollectionOfResourcesMatchingAUrlSortedByMetadata()
	{
		$options[TONIC_SORT_BY_METADATA] = array(
			'modified'
		);
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(array_keys($selectedResourceData), array(
			'/test/2', '/test/1', '/test/another'
		));
	}
	
	function testGetACollectionOfResourcesMatchingAUrlSortedByMetadataInReverseOrder()
	{
		$options[TONIC_SORT_BY_METADATA] = array(
			'modified DESC'
		);
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(array_keys($selectedResourceData), array(
			'/test/another', '/test/1', '/test/2'
		));
	}
	
	function testGetACollectionOfResourcesBeforeAGivenOffset()
	{
		$options[TONIC_FIND_TO] = 3;
		$selectedResourceData =& $this->adapter->select('/offset/', $options);
		$this->assertEqual(count($selectedResourceData), 3);
	}
	
	function testGetACollectionOfResourcesAfterAGivenOffset()
	{
		$options[TONIC_FIND_FROM] = 3;
		$selectedResourceData =& $this->adapter->select('/offset/', $options);
		$this->assertEqual(count($selectedResourceData), 2);
	}
	
	function testGetACollectionOfResourcesBeforeAndAfterAGivenOffset()
	{
		$options[TONIC_FIND_TO] = 2;
		$options[TONIC_FIND_FROM] = 3;
		$selectedResourceData =& $this->adapter->select('/offset/', $options);
		$this->assertEqual(count($selectedResourceData), 2);
	}
	
	function testGetACollectionOfResourcesBeforeAndAfterAGivenOffsetAndCaptureTotalNumber()
	{
		$options[TONIC_FIND_TO] = 2;
		$options[TONIC_FIND_FROM] = 3;
		$options[TONIC_CALC_FOUND_RESOURCES] = TRUE;
		$selectedResourceData =& $this->adapter->select('/offset/', $options);
		$this->assertEqual($this->adapter->foundResources(), 4);
	}
	
	function testInsertingANewResource()
	{
		$data = array(
			'url' => '/inserted',
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111111111',
			'title' => 'Inserted',
			'representation' => array('/inserted.html', '/inserted.en', '/inserted.en.html', '/inserted.json'),
			'content' => 'This is some inserted data'
		);
		$resource =& Resource::factory($this->adapter, $data);
		$this->assertTrue($this->adapter->insert($resource));
		$this->assertEqual(
			file_get_contents($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'inserted'),
			"url: /inserted\nclass: resource\nmimetype: application/tonic-resource\nmodified: 123456789\ncreated: 111111111\ntitle: Inserted\nrepresentation: /inserted.html\nrepresentation: /inserted.en\nrepresentation: /inserted.en.html\nrepresentation: /inserted.json\n\nThis is some inserted data"
		);
	}
	
	function testUpdatingAResource()
	{
		$data = array(
			'url' => '/test1',
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111111111',
			'title' => 'Updated'
		);
		$resource =& Resource::factory($this->adapter, $data);
		$this->assertTrue($this->adapter->update($resource));
		$this->assertEqual(
			file_get_contents($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'test1'),
			"url: /test1\nclass: resource\nmimetype: application/tonic-resource\nmodified: 123456789\ncreated: 111111111\ntitle: Updated\n"
		);
	}
	
	function testDeletingAResource()
	{
		$fp = fopen($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'delete', 'w');
		fwrite($fp, "url: /delete\n\nDelete me");
		fclose($fp);
		$this->assertTrue($this->adapter->delete('/delete'));
		$this->assertFalse(is_file($this->tmp.DIRECTORY_SEPARATOR.'tonic-test'.DIRECTORY_SEPARATOR.'delete'));
	}
	
	function testMountingAdapterAtAnArbitraryPointInTheUrlSpace()
	{
		$adapter =& new FileAdapter($this->mimetypes, $this->tmp.DIRECTORY_SEPARATOR.'tonic-test', '/foo/bar');
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $adapter->select('/foo/bar/test', $options);
		$this->assertEqual($selectedResourceData['/foo/bar/test']['url'], '/foo/bar/test');
	}
	
	function testMountingAdapterAtAnArbitraryPointInTheUrlSpaceAndGettingTheRootResource()
	{
		$adapter =& new FileAdapter($this->mimetypes, $this->tmp.DIRECTORY_SEPARATOR.'tonic-test', '/foo/bar');
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $adapter->select('/foo/bar', $options);
		$this->assertEqual($selectedResourceData['/foo/bar']['url'], '/foo/bar');
	}
	
	function testGettingAResourceWithNoMetadata()
	{
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $this->adapter->select('/no-metadata', $options);
		$this->assertEqual($selectedResourceData['/no-metadata']['url'], '/no-metadata');
		$this->assertEqual($selectedResourceData['/no-metadata']['content'], 'This contains nothing but content');
	}
	
	//*/
}

?>
