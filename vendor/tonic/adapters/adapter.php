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

// $Id: adapter.php 31 2007-11-25 11:11:14Z peejeh $

define('TONIC_FIND_EXACT', 1);
define('TONIC_FIND_DEFAULT_METADATA', 9);
define('TONIC_FIND_FORCE_METADATA', 2);
define('TONIC_FIND_BY_METADATA', 3);
define('TONIC_SORT_BY_METADATA', 4);
define('TONIC_FIND_FROM', 5);
define('TONIC_FIND_TO', 6);
define('TONIC_CALC_FOUND_RESOURCES', 7);
define('TONIC_FIND_BY_SQL', 8);

/**
 * Abstract persistance adapter.
 * @package Tonic/Adapters
 * @version $Revision: 31 $
 * @author Paul James
 * @abstract
 */
class Adapter
{
	/**
	 * @var str[]
	 */
	var $mimetypes = array();
	
	/**
	 * Number of resources found before applying an offset limit
	 * @var int
	 */
	var $foundResources = 0;
    
	/**
	 * @param str[] mimetypes The mimetypes to use for the adapter
	 */
	function adapter(&$mimetypes)
	{
		$this->mimetypes = $mimetypes;
	}
	
	/**
	 * Given a mimetype, return the file extension that goes with it
	 * @param str mimetype
	 * @return str
	 */
	function mimetypeToExtension($mimetype)
	{
		if ($found = array_search($mimetype, $this->mimetypes)) {
			return $found;
		}
		return NULL;
	}
	
	/**
	 * Given an extension, return the mimetype that goes with it
	 * @param str extension
	 * @return str
	 */
	function extensionToMimetype($extension)
	{
		if (isset($this->mimetypes[$extension])) {
			return $this->mimetypes[$extension];
		}
		return NULL;
	}
	
	/**
	 * Explode a URL into a base part and an extension if the URL has an extension part (following a dot)
	 * @param str url
	 * @return str[]
	 */
	function explodeUrlToGetExtensions($url)
	{
		$urlParts = explode('/', $url);
		$extensions = explode('.', array_pop($urlParts));
		return array(
			join('/', $urlParts).'/'.array_shift($extensions),
			$extensions
		);
	}
	
    /**
     * Select data from the data source
     * @param str url The URL to select on
     * @param str[] options An array of select options
     * @return str[][] An array of resource data arrays
     */
    function select($url, $options = array())
    {
        return array();
    }
	
	/**
	 * Get the number of resources found before applying an offset limit
	 * @return int
	 */
	function foundResources()
	{
		return $this->foundResources;
	}
    
    /**
     * Insert a resource
     * @param Resource resource
     * @return bool
     */
    function insert(&$resource)
    {
        return FALSE;
    }

    /**
     * Update a resource
     * @param Resource resource
     * @return bool
     */
    function update(&$resource)
    {
        return FALSE;
    }
    
    /**
     * Delete a resource
     * @param str url
     * @return bool
     */
    function delete($url)
    {
        return FALSE;
    }
}

?>
