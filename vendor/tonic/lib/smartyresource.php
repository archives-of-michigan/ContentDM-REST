<?php
/*
Tonic: A simple RESTful Web publishing and development system
Copyright (C) 2007 Paul James <paul@peej.co.uk>

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

// $Id: smartyresource.php 36 2008-01-15 22:39:21Z peejeh $

require_once 'resource.php';

/**
 * The Smarty templated resource class.
 * @package Tonic/Lib
 * @version $Revision: 36 $
 */
class SmartyResource extends Resource
{
	
	/**
	 * The Smarty object used by this resource
	 * @var Smarty
	 */
	var $_smarty;
	
	function smartyResource(&$adapter, $data)
	{
		parent::resource($adapter, $data);
		
		if (!class_exists('Smarty')) {
			require_once 'smarty'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'Smarty.class.php';
		}
		$this->_smarty =& new Smarty();
		$this->_smarty->template_dir = 'resources';
		$this->_smarty->compile_dir = 'scratch';
		$this->_smarty->cache_dir = 'scratch';
        $this->_smarty->error_reporting = E_ALL ^ E_NOTICE;
        
        $this->_smarty->register_resource('tonic', array(&$this, '_processGetTemplate', '_processGetTimestamp', '_processGetSecure', '_processGetTrusted'));
        $this->_smarty->register_modifier('process', array(&$this, '_processWithSmartyModifier'));
		
		$this->_smarty->assign_by_ref('this', $this);
		$this->_smarty->assign_by_ref('adapter', $this->_adapter);
    }
	
	/**
	 * Assign the given resource to this resource as being the data that this resource
	 * is here to display.
	 * @param Resource resource
	 */
	function assignParentResource(&$resource) {
		parent::assignParentResource($resource);
		$this->_smarty->assign_by_ref('resource', $this->_resource);
	}
	
    /**
     * Handler functions for the new "tonic" template type that is used to simply take a string.
     * and pass it to Smarty. This Smarty resource type should only be used from within this
     * class to enable the "process" modifier and handle cases where no template is used but we
     * still want to parse the resource body for Smarty tags.
     */
    function _processGetTemplate($name, &$source, &$smarty)
    {
        $source = $this->$name;
        return TRUE;
    }
    
    function _processGetTimestamp($name, &$timestamp, &$smarty)
    {
        $timestamp = time();
        return TRUE;
    }
    
    function _processGetSecure($name, &$smarty)
    {
        return TRUE;
    }
    
    function _processGetTrusted($name, &$smarty) {}
    
    /**
     * Process with Smarty modifier
     */
    function _processWithSmartyModifier($string)
    {
        $random = '_'.md5($string);
        $this->$random = $string;
        return $this->_smarty->fetch('tonic:'.$random, $this->url, $this->url);
    }
	
	/**
	 * Output the content of a resource using Smarty.
	 * @param Resource resource
	 */
	function _getContent()
	{
		if (isset($this->content)) {
			return $this->_smarty->fetch('tonic:content', $this->url, $this->url);
		}
		return NULL;
	}
}

?>
