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

// $Id: resource.php 36 2008-01-15 22:39:21Z peejeh $

/**
 * The base resource class. Models a HTTP resource and the methods that can be preformed upon it.
 * @package Tonic/Lib
 * @version $Revision: 36 $
 */
class Resource
{
    
	/**
	 * The adapter this resource uses
	 * @var Adapter
	 */
	var $_adapter;
	
	/**
	 * Does this resource exist in its adapter?
	 * @var bool
	 */
	var $_exists = FALSE;
	
	/**
	 * The representation for this resource that best matches the request
	 * @var str
	 */
	var $_representation = NULL;
	
    /**
     * Constructor
	 * @param Adapter adapter
	 * @param str[] data
     */
    function resource(&$adapter, $data)
    {
		$this->_adapter =& $adapter;
		
        foreach ($data as $field => $value) {
			$this->set($field, $value);
		}
		
		$class = 'class';
		if (isset($this->$class)) {
			$this->$class = strtolower($this->$class);
		} else {
			$this->$class = strtolower(get_class($this));
		}
		if (!isset($this->mimetype)) {
			if (isset($this->url)) {
				list($baseUrl, $extensions) = $adapter->explodeUrlToGetExtensions($this->url);
				$extension = array_shift($extensions);
			}
			if (!isset($extension) || !$this->mimetype = $adapter->extensionToMimetype($extension)) {
				$this->mimetype = 'application/tonic-resource';
			}
		}
		if (!isset($this->created)) {
			$this->created = time();
		}
		if (!isset($this->modified)) {
			$this->modified = time();
		}
    }
	
	function __toString() {
		return Resource::encodeResourceIntoTonicFormat($this);
	}
	
	/**
	 * Build a resource using the given data
	 * @static
	 * @param Adapter adapter
	 * @param str[] data
	 * @return Resource
	 */
	function &factory(&$adapter, $data)
	{
		if (isset($data['class'])) {
			$className = $data['class'];
		} else {
			//$className = $adapter->getClassName();
			$className = 'Resource';
		}
		Resource::loadResourceClass($className);
		if (class_exists($className)) {
			$resource =& new $className($adapter, $data);
		}
		if (!isset($resource)) { // can't load class or find object so build default
			$resource =& new Resource($adapter, $data);
		}
		return $resource;
	}
	
	/**
	 * Load the class file for the given classname
	 * @static
	 * @param str className
	 */
	function loadResourceClass($className)
	{
		if (!class_exists($className)) {
			require_once strtolower($className).'.php';
		}
	}
	
	/**
	 * Does this resource exist?
	 * @return bool
	 */
	function exists()
	{
		return $this->_exists;
	}
	
	/**
	 * Set a piece of resource data
	 * @param str field
	 * @param str value
	 * @return bool
	 */
	function set($field, $value)
	{
		if ($field && substr($field, 0, 1) != '_') {
			return $this->$field = $value;
		}
		return FALSE;
	}
	
	/**
	 * Get the data fields that this resource has
	 * @return str[]
	 */
	function getDataFields()
	{
		return array_keys(get_class_vars(get_class($this)));
	}
	
	/**
	 * Find a single resource that matches the given URL
	 * @static
	 * @param Adapter adapter
	 * @param str url
	 * @param str[] options
	 * @return Resource
	 */
	function &find(&$adapter, $url, $options = array())
	{
		$options[TONIC_FIND_EXACT] = TRUE;
		$resources =& Resource::findAll($adapter, $url, $options);
		if (count($resources)) {
			$return = array_pop($resources);
		} else {
			$return = NULL;
		}
		return $return;
	}
	
	/**
	 * Find a collection of resources that matches the beginnings of the given URL
	 * @static
	 * @param Adapter adapter
	 * @param str url
	 * @param str[] options
	 * @return Resource[]
	 */
	function &findAll(&$adapter, $url, $options = array())
	{
		$return = array();
		$resourcesData =& $adapter->select($url, $options);
		if (count($resourcesData)) {
			foreach ($resourcesData as $resourceData) {
				// apply default options
				if (isset($options[TONIC_FIND_DEFAULT_METADATA]) && is_array($options[TONIC_FIND_DEFAULT_METADATA])) {
					$resourceData = array_merge($options[TONIC_FIND_DEFAULT_METADATA], $resourceData);
				}
				// apply overwrite options
				if (isset($options[TONIC_FIND_FORCE_METADATA]) && is_array($options[TONIC_FIND_FORCE_METADATA])) {
					$resourceData = array_merge($resourceData, $options[TONIC_FIND_FORCE_METADATA]);
				}
				// create resource
				$resource =& Resource::factory($adapter, $resourceData);
				$resource->_exists = TRUE; // access private var!!
				$return[$resource->url] =& $resource;
			}
		}
		return $return;
	}
	
	/**
	 * Generate an entity tag value for this resource
	 * @return str
	 */
	function entityTag()
	{
		return md5($this->modified);
	}
	
	/**
	 * Output the default Tonic representation format
	 * @static
	 * @param Resource resource
	 * @return str
	 */
	function &encodeResourceIntoTonicFormat(&$resource)
	{
		$tonicFormat = '';
		foreach (get_object_vars($resource) as $field => $value) {
			if ($field != 'content' && substr($field, 0, 1) != '_') {
				if (is_array($value)) {
					foreach ($value as $v) {
						$tonicFormat .= $field.': '.$v."\n";
					}
				} elseif (is_object($value)) {
					if (is_a($value, 'Resource')) {
						$tonicFormat .= $field.': '.$value->url."\n";
					}
				} else {
					$tonicFormat .= $field.': '.$value."\n";
				}
			}
		}
		if (isset($resource->content)) {
			$tonicFormat .= "\n".$resource->content;
		}
		return $tonicFormat;
	}
	
	/**
	 * Turn the default Tonic data format into PHP array
	 * @param str tonicFormat
	 * @return str[]
	 */
	function &decodeResourceFromTonicFormat($tonicFormat)
	{
		$data = array();
		$lines = explode("\n", $tonicFormat);
		$headerMatchRegex = '/^([a-zA-Z0-9_-]+):\s*(.+)$/';
		$inHeaders = preg_match($headerMatchRegex, $lines[0]);
		foreach ($lines as $line) {
			if ($inHeaders) {
				$line = trim($line);
				if (!preg_match($headerMatchRegex, $line, $parts)) {
					$inHeaders = FALSE;
					$inBody = TRUE;
				} elseif (isset($parts[1]) && isset($parts[2])) {
					if (isset($data[$parts[1]])) {
						if (is_array($data[$parts[1]])) {
							$data[$parts[1]][] = $parts[2];
						} else {
							$data[$parts[1]] = array($data[$parts[1]], $parts[2]);
						}
					} else {
						$data[$parts[1]] = $parts[2];
					}
				}
			} else {
				if (!isset($data['content'])) {
					$data['content'] = '';
				}
				$data['content'] .= $line."\n";
			}
		}
		if (isset($data['content'])) {
			$data['content'] = substr($data['content'], 0, -1);
		}
		return $data;
	}
	
	/**
     * Get the HTTP realm for this site being careful of PHP safe mode
     * @return str The HTTP realm
     */
    function getRealm($realm)
    {
        if (ini_get('safe_mode')) {
            return $realm.'-'.getmyuid();
        }
		return $realm;
    }
	
	/**
     * Get nonce values for HTTP Digest
	 * @param str privateKey The private key to use
     * @param int life The life of the nonce value in minutes
	 * @param str clientAddress The IP address of the requesting client
     * @return str A generated nonce value
     */
    function getNonce($privateKey, $life = 5, $clientAddress) {
        $time = mktime(date('H'), (floor(date('i') / $life) * $life) + $life, 0);
        return md5(date('Y-m-d H:i', $time).':'.$clientAddress.':'.$privateKey);
    }
	
	/**
     * Do HTTP basic authentication
	 * @param Request request
	 * @param str[] config
	 * @param str username The users username
	 * @param str password The users password
     * @return bool
     */
    function _authorisedByBasicAuth(&$request, &$config, $username, $password)
    {
        if (isset($request->basicAuth['username']) && isset($request->basicAuth['password'])) {
			return $username == $request->basicAuth['username'] && $password == $request->basicAuth['password'];
        }
        return FALSE;
    }
	
	/**
     * Do HTTP digest authentication
	 * @param Request request
	 * @param str[] config
	 * @param str username The users username
     * @param str password The users password
     * @return bool
     */
    function _authorisedByDigestAuth(&$request, &$config, $username, $password)
    {
		if (isset($request->digestAuth['username'])) {
			$url = $request->url;
			if (strpos($url, '?') !== FALSE) { // hack for IE which does not pass querystring in URI element of Digest string or in response hash
				$url = substr($url, 0, strlen($request->digestAuth['uri']));
			}
			if (
				md5($request->digestAuth['opaque']) == md5($config['opaque']) &&
				$request->digestAuth['uri'] == $url &&
				$request->digestAuth['nonce'] == $this->getNonce($config['privateKey'], $config['life'], $config['clientAddress'])
			) {
				$a1 = md5($request->digestAuth['username'].':'.$this->getRealm($config['realm']).':'.$password);
				$a2 = md5($request->method.':'.$url);
				if (
					isset($request->digestAuth['qop']) &&
					isset($request->digestAuth['nc']) &&
					isset($request->digestAuth['cnonce'])
				) {
					$expectedResponse = md5($a1.':'.$request->digestAuth['nonce'].':'.$request->digestAuth['nc'].':'.$request->digestAuth['cnonce'].':'.$request->digestAuth['qop'].':'.$a2);
				} else {
					$expectedResponse = md5($a1.':'.$request->digestAuth['nonce'].':'.$a2);
				}
				return $request->digestAuth['response'] == $expectedResponse;
			}
		}
        return FALSE;
    }
	
	/**
     * Do HTTP cookie authentication
	 * @param Request request
	 * @param str[] config
	 * @param str username The users username
	 * @param str password The users password
     * @return bool
     */
    function _authorisedByCookieAuth(&$request, &$config, $username, $password)
    {
        if (isset($request->cookieAuth['username']) && isset($request->cookieAuth['hash'])) {
			return
				$username == $request->cookieAuth['username'] &&
				md5($username.$this->getNonce(
					$config['privateKey'],
					$config['life'],
					$config['clientAddress']
				)) == $request->cookieAuth['hash'];
        }
        return FALSE;
    }
	
	/**
	 * Get the raw body content for this resource
	 * @return str
	 */
	function _getContent()
	{
		if (isset($this->content)) {
			return $this->content;
		}
		return NULL;
	}
	
	/**
	 * Get the URL of the representation to use for this resource
	 * @param Request request
	 * @return str
	 */
	function &_getRepresentationURL(&$request)
	{
		$url = NULL;
		if ($request->extensions && isset($this->representation)) {
			if (!is_array($this->representation)) {
				$this->representation = array($this->representation);
			}
			foreach ($request->extensions as $extension) {
				foreach ($this->representation as $representationUrl) {
					if (substr($representationUrl, -strlen($extension)) == $extension) {
						$url = $representationUrl;
						break 2;
					}
				}
			}
		}
		return $url;
	}
	
	/**
	 * Save the resource to the persistance adapter
	 * @return bool
	 */
	function save()
	{
		if ($this->_exists) {
			return $this->_adapter->update($this);
		} else {
			if ($this->_adapter->insert($this)) {
				$this->_exists = TRUE;
				return TRUE;
			}
			return FALSE;
		}
	}
	
	/**
	 * Remove the resource from the persistance adapter
	 * @return bool
	 */
	function remove()
	{
		if ($this->_adapter->delete($this->url)) {
			$this->_exists = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Does the request match the resources etag or modified date?
	 * @param Request request
	 * @return bool
	 */
	function etagOrUnmodified(&$request) {
		return (!$request->ifMatch && $request->ifUnmodifiedSince == 0) ||
			in_array('*', $request->ifMatch) && $this->exists() ||
			in_array($this->entityTag(), $request->ifMatch) ||
			(int)$this->modified <= $request->ifUnmodifiedSince;
	}
	
	/**
	 * Does the request not match the resources etag or modified date?
	 * @param Request request
	 * @return bool
	 */
	function noEtagOrModified(&$request) {
		return !in_array('*', $request->ifNoneMatch) &&
			(!$this->entityTag() || !in_array($this->entityTag(), $request->ifNoneMatch)) &&
			($request->ifModifiedSince == 0 || (int)$this->modified > $request->ifModifiedSince);
	}
	
	/**
	 * Method to execute upon recieving a HEAD HTTP method from the client
	 * @param Request request The HTTP request
	 * @return Response The HTTP response
	 */
	function &head(&$request)
	{
		$response =& $this->get($request);
		$response->body = NULL;
		return $response;
	}
	
	/**
	 * Method to execute upon recieving a GET HTTP method from the client
	 * @param Request request The HTTP request
	 * @return Response The HTTP response
	 */
	function &get(&$request)
	{
		if ($this->noEtagOrModified($request)) { // if-none-match etag match
			if ($this->_exists || isset($this->_resource)) {
				/*if ($this->_notAuthorised($request, 'root', 'xyzzy')) {
					$response =& new Response(401);
					$response->sendAuthHeader();
					return $response;
				}*/
				if (!isset($this->_resource) && $representationUrl =& $this->_getRepresentationURL($request)) {
					list($baseUrl, $representationExtension) = $this->_adapter->explodeUrlToGetExtensions($representationUrl);
					$representationExtension = join('.', $representationExtension);
					if ($request->url != '/' && substr($request->url, -strlen($representationExtension)) != $representationExtension) {
						$response =& new Response(302);
						$response->headers['Location'] = $request->fullUrl.'.'.$representationExtension;
					} else {
						$this->_representation = $representationUrl;
						$response =& new Response(200);
					}
				} elseif (isset($this->mimetype) && $body = $this->_getContent()) { // is raw, so just output it
					if ($this->mimetype == 'application/tonic-resource') { // special case, seems like a code smell, TODO
						$response =& new Response(200, $this->encodeResourceIntoTonicFormat($this));
					} else {
						$response =& new Response(200, $body);
					}
				} else { // no matching representation
					if ($request->url == $this->url) { // we're after this resource, so output the default representation
						$response =& new Response(200, $this->encodeResourceIntoTonicFormat($this));
					} else { // no representations found that are acceptable
						$response =& new Response(406);
					}
				}
			} else {
				$response =& new Response(404); // not found
			}
		} else {
			$response =& new Response(304); // not modified
		}
		$response->setDefaultHeaders($this, $request);
		return $response;
	}
	
	/**
	 * Put the sent request body into the resource and generate a response
	 * @param Request request The HTTP request
	 * @return Response The HTTP response
	 */
	function &_updateResource(&$request)
	{
		// get data from request
		if ($request->data) { // update/create this resource
			// set data within resource
			foreach ($request->data as $field => $value) {
				$this->set($field, $value);
			}
			$this->set('modified', time());
			// save and generate response
			if ($this->_exists && $this->_adapter->update($this)) {
				$response =& new Response(204); // no content
			} elseif ($this->_adapter->insert($this)) {
				$response =& new Response(201); // created
			} else {
				$response =& new Response(500); // error
			}
		} else {
			$response =& new Response(411); // error
		}
		return $response;
	}
	
	/**
	 * Method to execute upon recieving a PUT HTTP method from the client
	 * @param Request request The HTTP request
	 * @param Adapter adapter
	 * @param str class
	 * @return Response The HTTP response
	 */
	function &put(&$request, &$adapter)
	{
		//$response =& $this->_updateResource($request, $adapter);
		$response =& new Response(405); // method not allowed
		return $response;
	}
	
	/**
	 * Append the sent request body to the resource and generate a response
	 * @param Request request The HTTP request
	 * @return Response The HTTP response
	 */
	function &_appendResource(&$request)
	{
		if (!$this->_exists) {
			$response =& new Response(404); // nothing here
		} elseif ($request->data) { // get data from request
			// create new resource
			$resource =& Resource::factory($this->_adapter, $request->data);
			// save and generate response
			if ($this->_adapter->insert($resource, $this->url)) {
				$response =& new Response(201); // created
				$response->headers['Location'] = $resource->url;
			} else {
				$response =& new Response(500); // error
			}
		} else {
			$response =& new Response(411); // error
		}
		return $response;
	}
	
	/**
	 * Method to execute upon recieving a POST HTTP method from the client
	 * @param Request request The HTTP request
	 * @return Response The HTTP response
	 */
	function &post(&$request)
	{
		//$response =& $this->_appendResource($request);
		$response =& new Response(405); // method not allowed
		return $response;
	}
	
	/**
	 * Delete the resource and generate a response
	 * @param Request request The HTTP request
	 * @return Response The HTTP response
	 */
	function &_deleteResource(&$request)
	{
		if (!$this->_exists) {
			$response =& new Response(404); // nothing here
		} elseif ($this->_adapter->delete($request->url)) {
			$response =& new Response(204); // no content
		} else {
			$response =& new Response(500); // error
		}
		return $response;
	}
	
	/**
	 * Method to execute upon recieving a DELETE HTTP method from the client
	 * @param Request request The HTTP request
	 * @return Response The HTTP response
	 */
	function &delete(&$request)
	{
		//$response =& $this->_deleteResource($request);
		$response =& new Response(405); // method not allowed
		return $response;
	}
	
	/**
	 * Load the representation for this resource
	 * @param Adapter adapter
	 * @param Options options
	 * @return Resource
	 */
	function &loadRepresentation(&$adapter, $options = array())
	{
		if (isset($this->_representation) && $representation =& Resource::find($adapter, $this->_representation, $options)) {
			$representation->assignParentResource($this);
		} else {
			$representation = NULL;
		}
		return $representation;
	}
	
	/**
	 * Assign the given resource to this resource as being the data that this resource
	 * is here to display.
	 * @param Resource resource
	 */
	function assignParentResource(&$resource) {
		$this->_resource =& $resource;
	}
    
}

?>
