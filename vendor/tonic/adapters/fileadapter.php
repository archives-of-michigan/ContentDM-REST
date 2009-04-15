<?php
/*
Tonic: A simple RESTful Web publishing and development system
Copyright (C) 2005 Paul James <paul@peej.co.uk>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// $Id: fileadapter.php 28 2007-11-10 23:30:17Z peejeh $

require_once 'adapter.php';

/**
 * File system persistance adapter.
 * @package Tonic/Adapters
 * @version $Revision: 28 $
 * @author Paul James
 */
class FileAdapter extends Adapter
{
	
	/**
	 * The URL-space to mount this adapter at
	 * @var str
	 */
	var $urlSpace;
	
	/**
	 * The filesystem path to use for the root of this adapter
	 * @var str
	 */
	var $path;
	
	/**
	 * The document name to use when a URL maps to a directory
	 * @var str
	 */
	var $defaultDocument;
	
	/**
	 * @param str[] mimetypes The mimetypes to use for the adapter
	 * @param str path The filesystem path to use for the adapter
	 * @param str urlSpace The optional URL-space position to mount this adapter at
	 */
    function fileAdapter(&$mimetypes, $path, $urlSpace = '', $defaultDocument = 'default.html')
    {
		parent::adapter($mimetypes);
		$this->urlSpace = $urlSpace;
		$this->path = $path;
		$this->defaultDocument = $defaultDocument;
		$parts = explode(DIRECTORY_SEPARATOR, $path);
		$path = '';
		foreach ($parts as $part) {
			$path .= $part.DIRECTORY_SEPARATOR;
			if (!is_dir($path)) {
				@mkdir($path);
			}
		}
    }
	
	/**
     * Select data from the data source
     * @param str url The URL to select on
     * @param str[] options An array of select options
     * @return str[][] An array of resource data arrays
     */
    function &select($url, $options = array())
    {
		$results = array();
		if (isset($options[TONIC_FIND_EXACT]) && $options[TONIC_FIND_EXACT]) {
			$filename = $this->_turnUrlIntoPath($url);
			if (is_file($filename)) {
				$data = $this->_getFileContents($filename);
				$results[$data['url']] = $data;
			} else return $results;
		} else {
			if (substr($url, -1, 1) == '/') {
				$path = $this->_turnUrlIntoPath($url.'*');
			} else {
				$path = $this->_turnUrlIntoPath($url.'/*');
			}
			$filenames = glob($path);
			foreach ($filenames as $filename) {
				if (is_file($filename) && substr($filename, -strlen($this->defaultDocument)) != $this->defaultDocument) {
					$data = $this->_getFileContents($filename);
					$results[$data['url']] = $data;
				} elseif (is_dir($filename)) {
					$filename .= DIRECTORY_SEPARATOR.$this->defaultDocument;
					if (is_file($filename)) {
						$data = $this->_getFileContents($filename);
						$results[$data['url']] = $data;
					}
				}
			}
		}
		// find by metadata
		if (isset($options[TONIC_FIND_BY_METADATA]) && is_array($options[TONIC_FIND_BY_METADATA])) {
			foreach ($options[TONIC_FIND_BY_METADATA] as $field => $value) {
				foreach ($results as $url => $result) {
					if ($result[$field] != $value) {
						unset($results[$url]);
					}
				}
			}
		}
		// calc found resources
		if (isset($options[TONIC_CALC_FOUND_RESOURCES]) && $options[TONIC_CALC_FOUND_RESOURCES]) {
			$this->foundResources = count($results);
		}
		// limit
		if (isset($options[TONIC_FIND_FROM]) && is_numeric($options[TONIC_FIND_FROM]) && isset($options[TONIC_FIND_TO]) && is_numeric($options[TONIC_FIND_TO])) {
			$results = array_slice($results, $options[TONIC_FIND_FROM] - 1, $options[TONIC_FIND_TO]);
		} elseif (isset($options[TONIC_FIND_FROM]) && is_numeric($options[TONIC_FIND_FROM])) {
			$results = array_slice($results, $options[TONIC_FIND_FROM] - 1, count($results) - 1);
		} elseif (isset($options[TONIC_FIND_TO]) && is_numeric($options[TONIC_FIND_TO])) {
			$results = array_slice($results, 0, $options[TONIC_FIND_TO]);
		}
		// order by metadata
		if (isset($options[TONIC_SORT_BY_METADATA])) {
			if (!is_array($options[TONIC_SORT_BY_METADATA])) {
				$options[TONIC_SORT_BY_METADATA] = array($options[TONIC_SORT_BY_METADATA]);
			}
			foreach ($options[TONIC_SORT_BY_METADATA] as $field) {
				$parts = explode(' ', $field);
				if (isset($parts[1]) && strtolower($parts[1]) == 'desc') {
					uasort($results, create_function('$a, $b', 'return ($a[\''.$parts[0].'\'] > $b[\''.$parts[0].'\']) ? -1 : 1;'));
				} else {
					uasort($results, create_function('$a, $b', 'return ($a[\''.$parts[0].'\'] < $b[\''.$parts[0].'\']) ? -1 : 1;'));
				}
			}
		}
		return $results;
    }
	
	/**
	 * Convert a URL into a fully qualified filesystem path
	 * @param str url
	 * @return str
	 */
	function _turnUrlIntoPath($url)
	{
		if (substr($url, -1, 1) == '/') { // strip trailing slashes
			$url = substr($url, 0, -1);
		}
		$path = $this->path.preg_replace('|/([^/]*)/|', '/$1/', substr($url, strlen($this->urlSpace)));
		if (is_dir($path)) {
			$path .= '/'.$this->defaultDocument;
		}
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}
	
	/**
	 * Get a file from the filesystem and decode it into an array
	 * @param str filename
	 * @return str[]
	 */
	function _getFileContents($filename)
	{
		$fileContents = file_get_contents($filename);
		$data = Resource::decodeResourceFromTonicFormat($fileContents);
		if (isset($data['url'])) {
			if ($data['url'] == '/' && $this->urlSpace) {
				$data['url'] = $this->urlSpace;
			} else {
				$data['url'] = $this->urlSpace.$data['url'];
			}
		} else {
			$data['url'] = $this->urlSpace.substr($filename, strlen($this->path));
		}
		if (!isset($data['created'])) {
			$data['created'] = filectime($filename);
		}
		if (!isset($data['modified'])) {
			$data['modified'] = filemtime($filename);
		}
		return $data;
	}
	
	/**
	 * Create a directory making all parent directories that don't exist as needed.
	 * @param str path
	 */
	function _mkdir($path)
	{
		$parts = explode(DIRECTORY_SEPARATOR, substr($path, strlen($this->path)));
		$path = $this->path;
		foreach ($parts as $part) {
			$path .= DIRECTORY_SEPARATOR.$part;
			@mkdir($path);
		}
	}
	
    /**
     * Insert a resource
     * @param Resource resource
     * @return bool
     */
    function insert(&$resource)
    {
		$path = $this->_turnUrlIntoPath($resource->url);
		$this->_mkdir(dirname($path));
		if ($fp = fopen($path, 'w')) {
			fwrite($fp, Resource::encodeResourceIntoTonicFormat($resource));
			fclose($fp);
			return TRUE;
		}
		return FALSE;
    }
	
    /**
     * Update a resource
     * @param Resource resource
     * @return bool
     */
    function update(&$resource)
    {
        return $this->insert($resource);
    }
    
    /**
     * Delete a resource
     * @param str url
     * @return bool
     */
    function delete($url)
    {
        $path = $this->_turnUrlIntoPath($url);
		return @unlink($path);
    }
}

?>
