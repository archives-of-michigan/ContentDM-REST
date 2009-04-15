#!/usr/bin/php
<?php
/*
Tonic development Web server
Copyright (C) 2006 Paul James <paul@peej.co.uk>

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

// $Id: serve.php 11 2007-02-25 22:30:54Z peejeh $

/*
This script is a very basic Web server that can be used to run Tonic
applications during development.

The default IP and port are 127.0.0.1:8888, these can be changed by adjusting
the constants defined below.

It expects to be run from the docroot of your application and for your
dispatcher script to be called dispatch.php

It requires access to the CGI version of PHP. If it is not available in the
usual places, you will need to adjust the path set in the constant defined
below.
*/

// Set the ip and port we will listen on
define('IP', '127.0.0.1');
define('PORT', 8888);

// Set path to PHP CGI binary
//define('PHP_CGI_WIN', 'C:\php\php.exe');
define('PHP_CGI_WIN', 'C:\Program Files\PHP4\php_cgi.exe');
//define('PHP_CGI_WIN', 'C:\Program Files\PHP5\php-cgi.exe');
define('PHP_CGI_UNIX', '/usr/bin/php-cgi');

// Set name of Tonic dispatcher
define('DISPATCHER', '/dispatch.php');

// Set docroot location
if (isset($argv[1])) {
	define('DOCROOT', $argv[1]);
} else {
    define('DOCROOT', getcwd());
}

// Send initial id message
echo "Tonic development Web server\n";

// Check environment is ok
if (version_compare(phpversion(), "4.3.0", "<")) {
    die("PHP 4.3.0 or above is required, please upgrade.\n");
} elseif (!is_file(DOCROOT.DISPATCHER)) {
    die("Could not find Tonic dispatcher at '".DOCROOT.DISPATCHER."', you might need to edit me to let me know where it is.\n");
}

// Set the PHP CGI path
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('PHP_CGI', PHP_CGI_WIN);
} else {
    define('PHP_CGI', PHP_CGI_UNIX);
}
if (!is_file(PHP_CGI)) {
    die("Could not find PHP CGI binary at '".PHP_CGI."', you might need to edit me to let me know where it is.\n");
} elseif (!function_exists('socket_create')) {
	if (@!dl('php_sockets.dll') && @!dl('sockets.so')) {
		die("Could not load PHP socket extension, you might need to enable it in your php.ini.\n");
	}
}

// Create a TCP Stream socket
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
// Bind the socket to an address/port
@socket_bind($sock, IP, PORT) or die("Could not bind to address, maybe I'm already running or you need to wait for the socket to timeout.\n");
// Start listening for connections
socket_listen($sock);

// Send server ready message
echo "Ready and waiting for requests at http://".IP.":".PORT."...\n";

// Output request info?
if (in_array('--extra', $argv) || in_array('-e', $argv)) {
    define('EXTRA_OUTPUT', TRUE);
    echo "Extra output turned on, I will output all HTTP request and response headers\n";
} else {
    define('EXTRA_OUTPUT', FALSE);
}

// Set server env variables
putenv('SERVER_SOFTWARE=Tonic development Web server');
putenv('SERVER_NAME='.IP);
putenv('SERVER_ADDR='.IP);
putenv('GATEWAY_INTERFACE=CGI/1.1');
putenv('SERVER_PROTOCOL=HTTP/1.1');
putenv('SERVER_PORT='.PORT);
putenv('DOCUMENT_ROOT='.DOCROOT);
putenv('SCRIPT_FILENAME='.DOCROOT.DISPATCHER);
putenv('PATH_TRANSLATED='.DOCROOT.DISPATCHER);
putenv('SCRIPT_NAME='.DISPATCHER);
putenv('REDIRECT_STATUS=200');

while (true) {
    
    // Reset env variables
    putenv('HTTP_HOST=');
    putenv('HTTP_USER_AGENT=');
    putenv('HTTP_ACCEPT=');
    putenv('HTTP_ACCEPT_ENCODING=');
    putenv('HTTP_ACCEPT_CHARSET=');
    putenv('HTTP_KEEP_ALIVE=');
    putenv('HTTP_CONNECTION=');
    putenv('HTTP_IF_MODIFIED_SINCE=');
    putenv('HTTP_IF_NONE_MATCH=');
    putenv('HTTP_AUTHORIZATION=');
    putenv('HTTP_CACHE_CONTROL=');
    putenv('HTTP_REFERER=');
    
    // Accept incoming requests and handle them as child processes
    $client = socket_accept($sock);
    $request = '';
    $requestBody = '';
    $requestMethod = NULL;
    $requestUri = NULL;
    $requestBodyLength = 0;
    
    while ($input = socket_read($client, 1024)) {
        // Append request data
        $request .= $input;
        $requestEndPos = strpos($request, "\r\n\r\n");
        if ($requestEndPos !== FALSE) {
            $requestLines = explode("\r\n", substr($request, 0, $requestEndPos));
            // Get request line
            $requestLine = explode(" ", array_shift($requestLines));
            $requestMethod = $requestLine[0];
            putenv('REQUEST_METHOD='.$requestMethod);
            // Get remote address
            socket_getsockname($client, $address);
            putenv('REMOTE_ADDR='.$address);
            // Get URL
            $url = parse_url(urldecode($requestLine[1]));
            $requestUri = $url['path'];
            putenv('REQUEST_URI='.urldecode($requestLine[1]));
            putenv('REDIRECT_URL='.$requestUri);
            // Get querystring
			if (isset($url['query'])) {
				putenv('QUERY_STRING='.$url['query']);
			} else {
				putenv('QUERY_STRING=');
			}
            // Output request string
            echo '[', date('r'), '] "', $requestMethod, ' ', $requestUri, ' HTTP/1.1"';
            if (EXTRA_OUTPUT) echo "\nRequest:";
            // Get request headers
            foreach ($requestLines as $line) {
                $parts = explode(":", $line);
                if ($parts[1] != '') {
                    $headerName = strtoupper(str_replace('-', '_', trim($parts[0])));
                    $headerValue = trim($parts[1]);
                    if (EXTRA_OUTPUT) echo "\n  ".$headerName.': '.$headerValue;
                    putenv('HTTP_'.$headerName.'='.$headerValue);
                    if ($headerName == 'CONTENT_TYPE') {
                        putenv($headerName.'='.$headerValue);
                    } elseif ($headerName == 'CONTENT_LENGTH' && is_numeric($headerValue) && $headerValue > 0) {
                        putenv($headerName.'='.$headerValue);
                        $requestBodyLength = $headerValue;
                    }
                }
            }
            // Get request body
            if ($requestBodyLength) {
                $requestBody = substr($request, $requestEndPos + 4);
                while (strlen($requestBody) < $requestBodyLength && $input = socket_read($client, 1024)) {
                    $request .= $input;
                    $requestBody .= $input;
                }
                if (EXTRA_OUTPUT) echo "\n------\n", $requestBody, "\n------";
            }
            // Request done
            break;
        }
    }
    
    if (isset($requestMethod) && isset($requestUri)) { // Valid request, send response
        
        $response = '';
        
        // Run PHP
        
        if ($cgi = proc_open(
            '"'.PHP_CGI.'"',
            array(0 => array("pipe", "r"), 1 => array("pipe", "w")),
            $pipes
        )) {
            if ($requestBody) {
                fwrite($pipes[0], $requestBody);
            }
            fclose($pipes[0]);
            while (!feof($pipes[1])) {
                $response .= fgets($pipes[1], 1024);
            }
            fclose($pipes[1]);
            proc_close($cgi);
        }
        
        // Massage response
        
        $response = trim($response);
        if (preg_match('/Status: ([0-9]{3})(.*?)\r\n/', $response, $matches)) {
            $status = $matches[1];
            $code = $matches[2] == '' ? ' OK' : $matches[2];
            $response = "HTTP/1.1 ".$status.$code."\r\n".$response."\r\n";
            $response = preg_replace('/Status: [0-9]{3}.*?\r\n/', '', $response);
        } else {
            $status = '200';
            $response = "HTTP/1.1 200 OK\r\n".$response."\r\n";
        }
        
        // Display output back to client
        socket_write($client, $response);
        
        // Close the client (child) socket
        socket_close($client);
        
        // Log request/response
        if (!EXTRA_OUTPUT) {
            echo ' ', $status, "\n";
        } else {
            $responseHeaders = explode("\r\n\r\n", $response);
            echo "\nResponse:\n  ", str_replace("\r\n", "\n  ", trim($responseHeaders[0])), "\n";
        }
    }
}

// Close the master sockets
socket_close($sock);

?>
