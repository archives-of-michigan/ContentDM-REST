<?php

/*
include Tonic files, we'll presume that they are in the PHP include path
because that makes things a little easier 
*/
require_once '/Users/dkastner/Downloads/tonic/tools/..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'request.php';
require_once '/Users/dkastner/Downloads/tonic/tools/..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'resource.php';
require_once '/Users/dkastner/Downloads/tonic/tools/..'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'response.php';
require_once '/Users/dkastner/Downloads/tonic/tools/..'.DIRECTORY_SEPARATOR.'adapters'.DIRECTORY_SEPARATOR.'fileadapter.php';

/*
we'll create a map of path extensions to mimetypes so that Tonic can
automagically send the correct mimetype header without us having to specify it
for each resource
*/
$mimetypes = array(
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

/*
create the default persistence adapter to grab our resources from, here we'll
use the file system and point it to the directory named "resources"
*/
$adapter =& new FileAdapter($mimetypes, 'resources');

// create a request object based upon the incoming HTTP request
$request =& new Request();

/*
load the resource mentioned in the request via the request URL and accept headers
*/
$resource =& $request->load($adapter);

/*
execute the resource within the context of the request, this has the effect of
calling the resources get/post/put/delete() method and returning a response object
*/
$response =& $request->exec($adapter, $resource);

/*
if our resource does not have a visible representation (its representation is purely
data), then we can load another resource as its representation format
*/
if ($resource && $representation =& $resource->loadRepresentation($adapter)) {
    $response =& $representation->get($request);
}

// output the response doing encoding as required
$response->output();

?>