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

// $Id: mysqladapter.php 34 2007-12-02 15:14:24Z peejeh $

require_once 'sqladapter.php';

/**
 * MySQL persistance adapter.
 * @package Tonic/Adapters
 * @version $Revision: 34 $
 * @author Paul James
 */
class MySQLAdapter extends SQLAdapter
{
	
	/**
	 * @param str hostname
	 * @param str username
	 * @param str password
	 * @param str database
	 * @param bool persistent
	 * @return bool
	 */
	function connect($hostname = 'localhost', $username = 'root', $password = '', $database = 'tonic', $persistent = FALSE)
	{
		if ($persistent) {
			$handle = mysql_pconnect($hostname, $username, $password);
		} else {
			$handle = mysql_connect($hostname, $username, $password);
		}
		if ($handle) {
			if (mysql_select_db($database, $handle)) {
				mysql_query("SET CHARACTER SET 'utf8'", $handle);
				$this->_handle =& $handle;
				$this->_setTableSchemaData();
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * Get the primary keys from the database table
	 */
	function _setTableSchemaData()
	{
		if (!$this->fields) {
			if ($result = $this->_query('DESCRIBE '.$this->getTable(), $this->_handle)) {
				if (!$this->primaryKeys) {
					$findPrimaryKeys = TRUE;
				} else {
					$findPrimaryKeys = FALSE;
				}
				if (!$this->datetimeFields) {
					$findDatetimeFields = TRUE;
				} else {
					$findDatetimeFields = FALSE;
				}
				while ($row = $this->_fetchRow($result)) {
					if ($findPrimaryKeys && $row['Key'] == 'PRI') {
						$this->primaryKeys[] = $row['Field'];
					}
					$this->fields[] = $row['Field'];
					if ($findDatetimeFields && ($row['Type'] == 'datetime' || $row['Type'] == 'date')) {
						$this->datetimeFields[] = $row['Field'];
					}
				}
			}
		}
	}
	
	/**
	 * Convert a database datetime string into a unix timestamp
	 * @param str date
	 * @return int
	 */
	function _dateToTimestamp($date)
	{
		return mktime(
			substr($date, 11, 2),
			substr($date, 14, 2),
			substr($date, 17, 2),
			substr($date, 5, 2),
			substr($date, 8, 2),
			substr($date, 0, 4)
		);
	}
	
	/**
	 * Convert a unix timestamp into a database datetime string
	 * @param int timestamp
	 * @return str
	 */
	function _timestampToDate($timestamp)
	{
		return date('Y-m-d h:i:s', $timestamp);
	}
    
    /**
     * Return a textual description of the last DB error
     * @return str
     */
    function _error()
    {
        return mysql_error($this->_handle);
    }
	
	/**
     * Execute the given query
     * @param str sql The SQL to execute
     * @return resultset
     */
    function _query($sql)
    {
		return mysql_query($sql, $this->_handle);
    }
    
    /**
     * Fetch a row from a result set
     * @param resultset result A MySQL result set
     * @return str[]
     */
    function _fetchRow(&$result)
    {
        return mysql_fetch_assoc($result);
    }
    
    /**
     * The number of rows that were changed by the most recent SQL statement
     * @return int
     */
    function _affectedRows()
    {
        return mysql_affected_rows($this->_handle);
    }
	
	/**
     * The value of any auto-increment values from the last insert statement
     * @return int
     */
    function _insertId()
    {
        return mysql_insert_id($this->_handle);
    }
	
	/**
     * Return the base SQL SELECT statement for this adapter
	 * @param str[] options An array of options
     * @return str
     */
	function _getBaseSelectStatement(&$options)
	{
		if (isset($options[TONIC_CALC_FOUND_RESOURCES]) && $options[TONIC_CALC_FOUND_RESOURCES]) {
			if ($this->fields) {
				return sprintf(
					'SELECT SQL_CALC_FOUND_ROWS '.join($this->fields, ', ').' FROM %s',
					$this->getTable()
				);
			} else {
				return sprintf(
					'SELECT SQL_CALC_FOUND_ROWS * FROM %s',
					$this->getTable()
				);
			}
		} else {
			 return parent::_getBaseSelectStatement($options);
		}
	}
	
	/**
     * Return the number of found rows in the previous SQL SELECT statement
     * @return int
     */
	function _getFoundRows()
	{
		$result = $this->_query('SELECT FOUND_ROWS()');
		return mysql_result($result, 0);
	}
    
    /**
     * Escape a string for the database
     * @param str string The string to escape
     * @return str
     */
    function _escape($string)
    {
        return mysql_escape_string($string);
    }
    
    /**
     * Escape a field name for the database
     * @param str string The string to escape
     * @return str
     */
    function _escapeFieldName($string)
    {
        return '`'.$this->_escape($string).'`';
    }
    
    /**
     * Wrap a string in delimiters for this database
     * @param str string The string to wrap in delimiters
     * @return str
     */
    function _delimitString($string)
    {
		if (is_numeric($string)) {
			return $string;
		} else {
			return '"'.$string.'"';
		}
    }
	
}

?>
