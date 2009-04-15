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

// $Id: sqladapter.php 36 2008-01-15 22:39:21Z peejeh $

require_once 'adapter.php';

/**
 * SQL persistance adapter.
 * @package Tonic/Adapters
 * @version $Revision: 36 $
 * @author Paul James
 * @abstract
 */
class SQLAdapter extends Adapter
{
	
	/**
	 * The database connection handle
	 * @var resource
	 */
	var $_handle;
	
	/**
	 * The MySQL table to use
	 * @var str
	 */
	var $table = NULL;
	
	/**
	 * Regular expression to pull the primary keys values from the given URL
	 * @var str
	 */
	var $keyValues = NULL;
	
	/**
	 * How to turn primary keys back into a URL
	 * @var str
	 */
	var $template;
	
	/**
	 * Name of primary keys as found in URL
	 * @var str[]
	 */
	var $primaryKeys;
	
	/**
	 * Name of fields in the database table
	 * @var str[]
	 */
	var $fields;
	
	/**
	 * Name of fields which are datetime fields and which we want to automatically convert to and from unix timestamps
	 * @var str[]
	 */
	var $datetimeFields;
	
	/**
	 * @param str[] mimetypes The mimetypes to use for the adapter
	 * @param str table The name of the database table to use
	 * @param str keyValues Regular expression to extract primary keys from URL
	 * @param str template How to turn primary keys back into a URL
	 * @param str[] primaryKeys Name of primary keys as found in URL
	 * @param str[] fields The fields of the resource to insert into the database
	 * @param str[] datetimeFields Fields which are datetime fields and which we want to automatically convert to and from unix timestamps
	 */
    function SQLAdapter(&$mimetypes, $table = 'resource', $keyValues = '%^/([0-9]+)%', $template = '/%d', $primaryKeys = array(), $fields = array() ,$datetimeFields = array())
    {
		parent::adapter($mimetypes);
		
		$this->table = $table;
		$this->keyValues = $keyValues;
		$this->template = $template;
		
		$this->primaryKeys = $primaryKeys;
		$this->fields = $fields;
		$this->datetimeFields = $datetimeFields;
    }
	
	/**
	 * Get the database table name
	 * @return str
	 */
	function getTable()
	{
		return $this->table;
	}
	
	/**
	 * Get the database key values
	 * @param str url The URL that the table name is embedded in if we're extracting it with a regular expression
	 * @return str
	 */
	function getKeyValues($url = NULL)
	{
		preg_match_all($this->keyValues, $url, $matches);
		if (isset($matches[1])) {
			return $matches[1];
		}
		return NULL;
	}
	
	/**
	 * @abstract
	 * @param str hostname
	 * @param str username
	 * @param str password
	 * @param str database
	 * @param bool persistent
	 * @return bool
	 */
	function connect($hostname = 'localhost', $username = 'root', $password = '', $database = 'tonic', $persistent = FALSE)
	{
		$this->_setTableSchemaData();
		return FALSE;
	}
	
	/**
	 * Get the primary keys from the database table
	 * @return str
	 */
	function _setTableSchemaData()
	{
		if (!$this->fields) {
			$this->fields = array();
			$this->primaryKeys = array();
			$this->datetimeFields = array();
		}
	}
	
    /**
     * Return a textual description of the last DB error
	 * @abstract
     * @return str
     */
    function _error()
    {
        return NULL;
    }
	
	/**
	 * Get the database connection
	 * @return resource
	 */
	function &getConnection()
	{
		return $this->_handle;
	}
	
	/**
     * Execute the given query
	 * @abstract
     * @param str sql The SQL to execute
     * @return resultset
     */
    function _query($sql)
    {
		return NULL;
    }
    
    /**
     * Fetch a row from a result set
	 * @abstract
     * @param resultset result A MySQL result set
     * @return str[]
     */
    function _fetchRow(&$result)
    {
        return array();
    }
    
    /**
     * The number of rows that were changed by the most recent SQL statement
	 * @abstract
     * @return int
     */
    function _affectedRows()
    {
        return NULL;
    }
	
    /**
     * The value of any auto-increment values from the last insert statement
	 * @abstract
     * @return int
     */
    function _insertId()
    {
        return NULL;
    }
	
	/**
	 * Build an SQL SELECT statement
	 * @param str[] options An array of options
	 * @param str[] where
	 * @param str[] order
	 * @param int limit
	 * @param int offset
     * @return str
	 */
	function _buildSelectStatement(&$options, $where, $order, $limit, $offset)
	{
		$sql = $this->_getBaseSelectStatement($options);
		$sql .= $this->_getWhereClause($where);
		$sql .= $this->_getOrderClause($order);
		$sql .= $this->_getLimitClause($limit, $offset);
		return $sql;
	}
	
	/**
     * Return the base SQL SELECT statement for this adapter
	 * @param str[] options An array of options
     * @return str
     */
	function _getBaseSelectStatement(&$options)
	{
		if ($this->fields) {
			return sprintf(
				'SELECT '.join($this->fields, ', ').' FROM %s',
				$this->getTable()
			);
		} else {
			return sprintf(
				'SELECT * FROM %s',
				$this->getTable()
			);
		}
	}
	
	function _getWhereClause($where)
	{
		if (count($where)) {
			return ' WHERE ('.join(') AND (', $where).')';
		}
	}
	
	function _getOrderClause($order)
	{
		if (count($order)) {
			return ' ORDER BY '.join(', ', $order);
		}
	}
	
	function _getLimitClause($limit, $offset)
	{
		if ($limit) {
			$sql = ' LIMIT ';
			if ($offset) {
				$sql .= $offset.', ';
			}
			return $sql.$limit;
		} elseif ($offset) {
			return ' LIMIT '.$offset.', 99999999999999999';
		}
	}
	
	/**
     * Return the number of found rows in the previous SQL SELECT statement
	 * @abstract
     * @return int
     */
	function _getFoundRows()
	{
		return NULL;
	}
    
    /**
     * Escape a string for the database
	 * @abstract
     * @param str string The string to escape
     * @return str
     */
    function _escape($string)
    {
        return NULL;
    }
    
    /**
     * Escape a field name for the database
	 * @abstract
     * @param str string The string to escape
     * @return str
     */
    function _escapeFieldName($string)
    {
        return NULL;
    }
    
    /**
     * Wrap a string in delimiters for this database
	 * @abstract
     * @param str string The string to wrap in delimiters
     * @return str
     */
    function _delimitString($string)
    {
        return NULL;
    }
	
	/**
	 * Turn the URL into the primary keys using the URL regex
	 * @param str url
	 * @return str[]
	 */
	function _makeKeys($url)
	{
		$keyValues = $this->getKeyValues($url);
		$keys = array();
		foreach ($this->primaryKeys as $id => $key) {
			if (isset($keyValues[$id])) {
				$keys[$key] = $keyValues[$id];
			}
		}
		return $keys;
	}
	
	/**
	 * Turn the primary keys into a URL using the URL template
	 * @param str[] data
	 * @return str
	 */
	function _makeUrl($data)
	{
		$idValues = array();
		foreach ($this->primaryKeys as $id) {
			$idValues[] = $data[$id];
		}
		return vsprintf($this->template, $idValues);
	}
	
	/**
	 * Convert a database datetime string into a unix timestamp
	 * @param str date
	 * @return int
	 */
	function _dateToTimestamp($date)
	{
		return $date;
	}
	
	/**
	 * Convert a unix timestamp into a database datetime string
	 * @param int timestamp
	 * @return str
	 */
	function _timestampToDate($timestamp)
	{
		return $timestamp;
	}
	
	/**
     * Select data from the data source
     * @param str url The URL of the resource to select
     * @param str[] options An array of options
     * @return str[]
     */
    function &select($url, $options = NULL)
    {
        $data = array();
		
		if (isset($options[TONIC_FIND_BY_SQL])) { // we've been given SQL, just go with it
			if (is_array($options[TONIC_FIND_BY_SQL])) {
				$sql = vsprintf(array_shift($options[TONIC_FIND_BY_SQL]), $options[TONIC_FIND_BY_SQL]);
			} else {
				$sql = $options[TONIC_FIND_BY_SQL];
			}
			
		} else { // process options and build SQL
			$where = array();
			$order = array();
			$limit = NULL;
			$offset = NULL;
			
			$keys = $this->_makeKeys($url);
			// find one
			if ($keys) {
				foreach ($keys as $id => $key) {
					$where[] = sprintf('%s.%s = %s', $this->getTable(), $this->_escapeFieldName($id), $this->_delimitString($this->_escape($key)));
				}
			}
			// find by metadata
			if (isset($options[TONIC_FIND_BY_METADATA]) && is_array($options[TONIC_FIND_BY_METADATA])) {
				if (is_array($options[TONIC_FIND_BY_METADATA])) {
					foreach ($options[TONIC_FIND_BY_METADATA] as $field => $value) {
						if (is_array($value)) {
							foreach ($value as $operator => $v) {
								if (is_scalar($v)) {
									$where[] = sprintf('%s.%s %s %s', $this->getTable(), $this->_escapeFieldName($field), $operator, $this->_delimitString($this->_escape($v)));
								} else {
									trigger_error('Can not use value "'.strval($v).'" as TONIC_FIND_BY_METADATA option', E_USER_ERROR);
								}
							}
						} elseif (is_scalar($value)) {
							$where[] = sprintf('%s.%s = %s', $this->getTable(), $this->_escapeFieldName($field), $this->_delimitString($this->_escape($value)));
						} else {
							trigger_error('Can not use value "'.strval($value).'" as TONIC_FIND_BY_METADATA option', E_USER_ERROR);
						}
					}
				} else {
					trigger_error('TONIC_FIND_BY_METADATA option is required to be an array', E_USER_ERROR);
				}
			}
			// find order
			if (isset($options[TONIC_SORT_BY_METADATA])) {
				if (is_array($options[TONIC_SORT_BY_METADATA])) {
					$order = array_merge($order, $options[TONIC_SORT_BY_METADATA]);
				} else {
					$order[] = $options[TONIC_SORT_BY_METADATA];
				}
			}
			// find start and end
			if (isset($options[TONIC_FIND_FROM]) && is_numeric($options[TONIC_FIND_FROM])) {
				$offset = $options[TONIC_FIND_FROM] - 1;
				if (isset($options[TONIC_FIND_TO]) && is_numeric($options[TONIC_FIND_TO])) {
					$limit = $options[TONIC_FIND_TO] - $options[TONIC_FIND_FROM] + 1;
				}
			} elseif (isset($options[TONIC_FIND_TO]) && is_numeric($options[TONIC_FIND_TO])) {
				$offset = 0;
				$limit = $options[TONIC_FIND_TO];
			}
			
			// build SQL
			$sql = $this->_buildSelectStatement($options, $where, $order, $limit, $offset);
		}
		//var_dump($sql);// die;
		
		// execute SQL
		$result = $this->_query($sql);
		if ($result) {
			while($row = $this->_fetchRow($result)) {
				$row['url'] = $this->_makeUrl($row);
				foreach ($row as $field => $value) { // assign metatdata values
					if (in_array($field, $this->datetimeFields)) {
						$data[$row['url']][$field] = $this->_dateToTimestamp($value);
					} elseif (substr($value, 0, 5) == 'srlz!') {
						$data[$row['url']][$field] = unserialize(substr($value, 6));
					} else {
						$data[$row['url']][$field] = $value;
					}
				}
			}
		} else {
			trigger_error($this->_error().' for URL "'.$url.'" with SQL "'.$sql.'"', E_USER_ERROR);
		}
		// grab found rows if asked to
		if (isset($options[TONIC_CALC_FOUND_RESOURCES]) && $options[TONIC_CALC_FOUND_RESOURCES]) {
			$this->foundResources = $this->_getFoundRows();
		}
        return $data;
    }
    
    /**
     * Insert a resource
     * @param Resource resource
     * @return bool
     */
    function insert(&$resource)
    {
		$names = '';
		$values = '';
		foreach ($this->fields as $field) {
			if ($value = $resource->$field) {
				$names .= $this->_escapeFieldName($field).', ';
				if (in_array($field, $this->datetimeFields)) { // datetime magic
					$values .= $this->_delimitString($this->_escape($this->_timestampToDate($value))).', ';
				} elseif (is_object($value) && is_a($value, 'Resource')) { // resource
					$values .= $this->_delimitString($this->_escape($value->url)).', ';
				} elseif (is_object($value) || is_array($value)) { // serialize
					$values .= $this->_delimitString($this->_escape('srlz!'.serialize($value))).', ';
				} else {
					$values .= $this->_delimitString($this->_escape($value)).', ';
				}
			}
		}
		$sql = sprintf(
			'INSERT INTO %s (%s) VALUES (%s)',
			$this->getTable(),
			substr($names, 0, -2),
			substr($values, 0, -2)
		);
		$result = $this->_query($sql);
		if (!$result) {
			trigger_error($this->_error(), E_USER_ERROR);
		}
		if ($this->_affectedRows()) {
			foreach ($this->primaryKeys as $id) {
				if (!isset($resource->$id)) {
					$resource->$id = $this->_insertId();
					break;
				}
			}
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
		$values = '';
		foreach ($this->fields as $field) {
			if ($value = $resource->$field) {
				$values .= $this->_escapeFieldName($field).' = ';
				if (in_array($field, $this->datetimeFields)) {
					$values .= $this->_delimitString($this->_escape($this->_timestampToDate($value))).', ';
				} elseif (is_object($value) && is_a($value, 'Resource')) {
					$values .= $this->_delimitString($this->_escape($value->url)).', ';
				} elseif (is_object($value) || is_array($value)) { // serialize
					$values .= $this->_delimitString('srlz!'.serialize($value)).', ';
				} else {
					$values .= $this->_delimitString($this->_escape($value)).', ';
				}
			}
		}
		$sql = sprintf(
			'UPDATE %s SET %s WHERE ',
			$this->getTable(),
			substr($values, 0, -2)
		);
		
		foreach ($this->primaryKeys as $key) {
			$sql .= sprintf(
				'%s.%s = %s AND ',
				$this->getTable(),
				$this->_escapeFieldName($key),
				$this->_delimitString($this->_escape($resource->$key))
			);
		}
		$sql = substr($sql, 0, -5);
		
		$result = $this->_query($sql);
		if (!$result) {
			trigger_error($this->_error().' for URL "'.$resource->url.'"', E_USER_ERROR);
		}
		if ($this->_affectedRows()) {
			return TRUE;
		}
	    return FALSE;
    }
    
    /**
     * Delete a resource
     * @param str url
     * @return bool
     */
    function delete($url)
    {
		$keys = $this->_makeKeys($url);
		$sql = sprintf(
			'DELETE FROM %s WHERE ',
			$this->getTable()
		);
		foreach ($this->primaryKeys as $key) {
			$sql .= sprintf(
				'%s.%s = %s AND ',
				$this->getTable(),
				$this->_escapeFieldName($key),
				$this->_delimitString($this->_escape($keys[$key]))
			);
		}
		$sql = substr($sql, 0, -5);
		$result = $this->_query($sql);
		if (!$result) {
			trigger_error($this->_error().'" for URL "'.$url.'"', E_USER_ERROR);
		}
		if ($this->_affectedRows()) {
			return TRUE;
		}
    }
}

?>
