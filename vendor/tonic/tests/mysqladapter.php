<?php

require_once 'lib'.DIRECTORY_SEPARATOR.'resource.php';
require_once 'adapters'.DIRECTORY_SEPARATOR.'mysqladapter.php';

/**
 * These tests test the deleting of resource responses via the request exec method.
 * It uses a mock adapter to retrieve resources.
 * @package Tonic/Tests
 * @version $Revision: 34 $
 * @author Paul James
 */
class TestMySQLAdapter extends UnitTestCase
{
	
	var $adapter;
	var $hostname = 'localhost';
	var $username = 'root';
	var $password = '';
	var $database = 'tonic_test';
	var $table = 'test';
	
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
	
	function testMySQLAdapter()
	{
        $this->UnitTestCase('MySQL Adapter');
		
    }
    
    function setUp()
    {
		$mysql = mysql_connect($this->hostname, $this->username, $this->password);
		mysql_select_db($this->database, $mysql);
		$sql = sprintf('CREATE TABLE %s (id INT PRIMARY KEY AUTO_INCREMENT, class VARCHAR(32), mimetype VARCHAR(64), created INT, modified INT, representation TEXT, content TEXT, datefield DATETIME)', $this->table);
		mysql_query($sql);
		
		$sql = array(
			array($this->table, 1, 'resource', 'application/tonic-resource', 123456789, 111111111, NULL, 'This is the resource body', '2007-12-01 01:00:00'),
			array($this->table, 2, 'resource', 'application/tonic-resource', 123456789, 111111111,  NULL, 'This is the magic root resource', '2007-12-01 01:00:00'),
			array($this->table, 3, 'resource', 'application/tonic-resource', 123456789, 111111112,  NULL, 'This is the resource body', '2007-12-01 01:00:00'),
			array($this->table, 4, 'resource', 'application/tonic-resource', 123456789, 111111111,  NULL, 'This is the resource body 2', '2007-12-01 01:00:00'),
			array($this->table, 5, 'resource', 'application/tonic-resource', 123456789, 111111113, NULL, 'This is the resource body', '2007-12-01 01:00:00'),
			array($this->table, 6, 'resource', 'application/tonic-resource', 123456789, 111111114, NULL, 'This is another body', '2007-12-01 01:00:00'),
			array($this->table, 7, 'resource', 'application/tonic-resource', 123456789, 111111111, NULL, 'Offset 1', '2007-12-01 01:00:00'),
			array($this->table, 8, 'resource', 'application/tonic-resource', 123456789, 111111111, NULL, 'Offset 2', '2007-12-01 01:00:00'),
			array($this->table, 9, 'resource', 'application/tonic-resource', 123456789, 111111111, NULL, 'Offset 3', '2007-12-01 01:00:00'),
			array($this->table, 10, 'resource', 'application/tonic-resource', 123456789, 111111111, NULL, 'Offset 4', '2007-12-01 01:00:00'),
			array($this->table, 11, NULL, NULL, 0, 0, NULL, 'This contains nothing but content', NULL)
		);
		foreach ($sql as $arguments) {
			mysql_query(vsprintf('INSERT INTO %s (id, class, mimetype, created, modified, representation, content, datefield) VALUES (%d, "%s", "%s", %d, %d, "%s", "%s", "%s")', $arguments));
		}
		mysql_close($mysql);
		
		$this->adapter =& new MySQLAdapter($this->mimetypes, $this->table, '%^/test/([0-9]+)%', '/test/%d', array('id'), array('id', 'class', 'mimetype', 'created', 'modified', 'representation', 'content', 'datefield'));
		$this->adapter->connect($this->hostname, $this->username, $this->password, $this->database) or trigger_error('Could not connect to DB');
	}
	
	function tearDown()
	{
		$mysql = mysql_connect($this->hostname, $this->username, $this->password);
		mysql_select_db($this->database, $mysql);
		$sql = sprintf('DROP TABLE %s', $this->table);
		mysql_query($sql, $mysql);
		mysql_close($mysql);
	}
	
	function testGetAResource()
	{
		$selectedResourceData =& $this->adapter->select('/test/1');
		$comparisonResourceData = array(
			'/test/1' => array(
				'id' => '1',
				'class' => 'resource',
				'mimetype' => 'application/tonic-resource',
				'created' => '123456789',
				'modified' => '111111111',
				'representation' => '',
				'content' => 'This is the resource body',
				'datefield' => '2007-12-01 01:00:00',
				'url' => '/test/1'
		));
		$this->assertEqual($selectedResourceData, $comparisonResourceData);
	}
	
	function testNoPrimaryKeysOrFieldsGiven()
	{
		$adapter =& new MySQLAdapter($this->mimetypes, $this->table, '%^/test/([0-9]+)%', '/test/%d', array(), array());
		$adapter->connect($this->hostname, $this->username, $this->password, $this->database) or trigger_error('Could not connect to DB');
		$selectedResourceData =& $adapter->select('/test/1');
		$comparisonResourceData = array(
			'/test/1' => array(
				'id' => '1',
				'class' => 'resource',
				'mimetype' => 'application/tonic-resource',
				'created' => '123456789',
				'modified' => '111111111',
				'representation' => '',
				'content' => 'This is the resource body',
				'datefield' => '1196470800',
				'url' => '/test/1'
		));
		$this->assertEqual($selectedResourceData, $comparisonResourceData);
	}
	
	function testDateFieldsGiven()
	{
		$adapter =& new MySQLAdapter($this->mimetypes, $this->table, '%^/test/([0-9]+)%', '/test/%d', array('id'), array('id', 'class', 'mimetype', 'created', 'modified', 'representation', 'content', 'datefield'), array('datefield'));
		$adapter->connect($this->hostname, $this->username, $this->password, $this->database) or trigger_error('Could not connect to DB');
		$selectedResourceData =& $adapter->select('/test/1');
		$comparisonResourceData = array(
			'/test/1' => array(
				'id' => '1',
				'class' => 'resource',
				'mimetype' => 'application/tonic-resource',
				'created' => '123456789',
				'modified' => '111111111',
				'representation' => '',
				'content' => 'This is the resource body',
				'datefield' => '1196470800',
				'url' => '/test/1'
		));
		$this->assertEqual($selectedResourceData, $comparisonResourceData);
	}
	
	function testGetTheResourceWithTrailingSlash()
	{
		$selectedResourceData =& $this->adapter->select('/test/1/');
		$this->assertEqual($selectedResourceData['/test/1']['url'], '/test/1');
	}
	
	function testGetACollectionOfResourcesMatchingAUrl()
	{
		$selectedResourceData =& $this->adapter->select('/test/');
		$this->assertEqual(count($selectedResourceData), 11);
	}
	
	function testGetACollectionOfResourcesMatchingAUrlLimitedByMetadata()
	{
		$options[TONIC_FIND_BY_METADATA] = array(
			'content' => 'This is the resource body 2'
		);
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(count($selectedResourceData), 1);
		$this->assertEqual($selectedResourceData['/test/4']['content'], 'This is the resource body 2');
	}
	
	function testGetACollectionOfResourcesMatchingAUrlSortedByMetadata()
	{
		$options[TONIC_SORT_BY_METADATA] = array(
			'modified'
		);
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(array_keys($selectedResourceData), array(
			'/test/11', '/test/1', '/test/2', '/test/4', '/test/7', '/test/8', '/test/9', '/test/10', '/test/3', '/test/5', '/test/6'
		));
	}
	
	function testGetACollectionOfResourcesMatchingAUrlSortedByMetadataInReverseOrder()
	{
		$options[TONIC_SORT_BY_METADATA] = array(
			'modified DESC'
		);
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(array_keys($selectedResourceData), array(
			'/test/6', '/test/5', '/test/3', '/test/1', '/test/2', '/test/4', '/test/7', '/test/8', '/test/9', '/test/10', '/test/11'
		));
	}
	
	function testGetACollectionOfResourcesBeforeAGivenOffset()
	{
		$options[TONIC_FIND_TO] = 3;
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(count($selectedResourceData), 3);
	}
	
	function testGetACollectionOfResourcesAfterAGivenOffset()
	{
		$options[TONIC_FIND_FROM] = 5;
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(count($selectedResourceData), 7);
	}
	
	function testGetACollectionOfResourcesBeforeAndAfterAGivenOffset()
	{
		$options[TONIC_FIND_FROM] = 3;
		$options[TONIC_FIND_TO] = 5;
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(count($selectedResourceData), 3);
	}
	
	function testGetACollectionOfResourcesBeforeAndAfterAGivenOffsetAndCaptureTotalNumber()
	{
		$options[TONIC_FIND_FROM] = 3;
		$options[TONIC_FIND_TO] = 5;
		$options[TONIC_CALC_FOUND_RESOURCES] = TRUE;
		$selectedResourceData =& $this->adapter->select('/test/', $options);
		$this->assertEqual(count($selectedResourceData), 3);
		$this->assertEqual($this->adapter->foundResources(), 11);
	}
	
	function testInsertingANewResource()
	{
		$data = array(
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111111111',
			'representation' => array('/inserted.html', '/inserted.en', '/inserted.en.html', '/inserted.json'),
			'content' => 'This is some inserted data',
			'datefield' => '2007-12-01 01:00:00'
		);
		$resource =& Resource::factory($this->adapter, $data);
		$this->assertTrue($this->adapter->insert($resource));
		$mysql = mysql_connect($this->hostname, $this->username, $this->password);
		mysql_select_db($this->database, $mysql);
		$sql = sprintf('SELECT * FROM %s WHERE id = %d', $this->table, $resource->id);
		//var_dump($sql);die;
		$result = mysql_query($sql, $mysql);
		$fetchedData = mysql_fetch_assoc($result);
		unset($fetchedData['id']);
		$fetchedData['representation'] = unserialize(substr($fetchedData['representation'], 5));
		mysql_close($mysql);
		$this->assertEqual($fetchedData, $data);
	}
	
	function testInsertingANewResourceWithMagicDates()
	{
		$adapter =& new MySQLAdapter($this->mimetypes, $this->table, '%^/test/([0-9]+)%', '/test/%d', array('id'), array('id', 'class', 'mimetype', 'created', 'modified', 'representation', 'content', 'datefield'), array('datefield'));
		$adapter->connect($this->hostname, $this->username, $this->password, $this->database) or trigger_error('Could not connect to DB');
		$data = array(
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111111111',
			'representation' => '/inserted.html',
			'content' => 'This is some inserted data',
			'datefield' => '123456789'
		);
		$resource =& Resource::factory($adapter, $data);
		$this->assertTrue($adapter->insert($resource));
		$mysql = mysql_connect($this->hostname, $this->username, $this->password);
		mysql_select_db($this->database, $mysql);
		$sql = sprintf('SELECT * FROM %s WHERE id = %d', $this->table, $resource->id);
		$result = mysql_query($sql, $mysql);
		$fetchedData = mysql_fetch_assoc($result);
		unset($fetchedData['id']);
		$data['datefield'] = '1973-11-29 09:33:09';
		mysql_close($mysql);
		$this->assertEqual($fetchedData, $data);
	}
	
	function testUpdatingAResource()
	{
		$data = array(
			'id' => 1,
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111114444'
		);
		$resultData = array(
			'id' => 1,
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111114444',
			'representation' => '',
			'content' => 'This is the resource body',
			'datefield' => '2007-12-01 01:00:00'
		);
		$resource =& Resource::factory($this->adapter, $data);
		$this->assertTrue($this->adapter->update($resource));
		$mysql = mysql_connect($this->hostname, $this->username, $this->password);
		mysql_select_db($this->database, $mysql);
		$sql = sprintf('SELECT * FROM %s WHERE id = 1', $this->table);
		$result = mysql_query($sql, $mysql);
		$fetchedData = mysql_fetch_assoc($result);
		mysql_close($mysql);
		$this->assertEqual($fetchedData, $resultData);
	}
	
	function testUpdatingAResourceWithMagicDates()
	{
		$adapter =& new MySQLAdapter($this->mimetypes, $this->table, '%^/test/([0-9]+)%', '/test/%d', array('id'), array('id', 'class', 'mimetype', 'created', 'modified', 'representation', 'content', 'datefield'), array('datefield'));
		$adapter->connect($this->hostname, $this->username, $this->password, $this->database) or trigger_error('Could not connect to DB');
		$data = array(
			'id' => 1,
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111114444',
			'datefield' => '123456789'
		);
		$resultData = array(
			'id' => 1,
			'class' => 'resource',
			'mimetype' => 'application/tonic-resource',
			'modified' => '123456789',
			'created' => '111114444',
			'representation' => '',
			'content' => 'This is the resource body',
			'datefield' => '1973-11-29 09:33:09'
		);
		$resource =& Resource::factory($adapter, $data);
		$this->assertTrue($adapter->update($resource));
		$mysql = mysql_connect($this->hostname, $this->username, $this->password);
		mysql_select_db($this->database, $mysql);
		$sql = sprintf('SELECT * FROM %s WHERE id = 1', $this->table);
		$result = mysql_query($sql, $mysql);
		$fetchedData = mysql_fetch_assoc($result);
		mysql_close($mysql);
		$this->assertEqual($fetchedData, $resultData);
	}
	
	function testDeletingAResource()
	{
		$mysql = mysql_connect($this->hostname, $this->username, $this->password);
		mysql_select_db($this->database, $mysql);
		$sql = sprintf('INSERT INTO %s (id, content) VALUES (13, "Delete me")', $this->table);
		mysql_query($sql, $mysql);
		$this->assertTrue($this->adapter->delete('/test/13'));
		$sql = sprintf('SELECT * FROM %s WHERE id = 13', $this->table);
		$result = mysql_query($sql, $mysql);
		$fetchedData = mysql_fetch_assoc($result);
		$this->assertFalse($fetchedData);
		mysql_close($mysql);
	}
	
	function testMountingAdapterAtAnArbitraryPointInTheUrlSpace()
	{
		$this->adapter->regex = '%^/foo/bar/test/([0-9]+)%';
		$this->adapter->template = '/foo/bar/test/%d';
		$options[TONIC_FIND_EXACT] = TRUE;
		$selectedResourceData =& $this->adapter->select('/foo/bar/test/1', $options);
		$this->assertEqual($selectedResourceData['/foo/bar/test/1']['url'], '/foo/bar/test/1');
	}
	
	//*/
}

?>
