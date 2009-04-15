<?php

require_once 'adapters'.DIRECTORY_SEPARATOR.'adapter.php';

/**
 * Mock adapter
 * @package Tonic/Tests/Mocks
 * @version $Revision: 28 $
 */
class MockAdapter extends Adapter
{
	
	var $resources = array();
	var $index = 0;
	
	function &select($url, $options = array())
	{
		$data = array();
		if (isset($options[TONIC_FIND_EXACT]) && $options[TONIC_FIND_EXACT]) {
			if (isset($this->resources[$url])) {
				$data[$url] = $this->resources[$url];
				if (!isset($data[$url]['url'])) {
					$data[$url]['url'] = $url;
				}
			}
		} else {
			foreach ($this->resources as $u => $resource) {
				if (substr($u, 0, strlen($url)) == $url) {
					$data[$u] = $this->resources[$u];
					if (!isset($data[$u]['url'])) {
						$data[$u]['url'] = $u;
					}
				}
			}
		}
		return $data;
	}
	
	function insert(&$resource, $url = NULL)
	{
		$return = $this->update($resource, $url);
		$this->resources[$resource->url]['created'] = time();
		return $return;
	}
	
	function update(&$resource, $url = NULL)
	{
		if (!isset($resource->url)) {
			$resource->url = $url.'/'.++$this->index;
		}
		foreach (get_object_vars($resource) as $field => $value) {
			if ($field && substr($field, 0, 1) != '_') {
				$this->resources[$resource->url][$field] = $value;
			}
		}
		$this->resources[$resource->url]['modified'] = time();
		return TRUE;
	}
	
	function delete($url)
	{
		if (isset($this->resources[$url])) {
			unset($this->resources[$url]);
			return TRUE;
		}
		return FALSE;
	}
}
?>
