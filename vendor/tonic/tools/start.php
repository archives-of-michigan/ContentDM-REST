#!/usr/bin/php
<?php
/*
Tonic start script
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

// $Id: start.php 29 2007-11-20 23:14:02Z peejeh $

/*
This script creates a new Tonic project.
*/

function startMkdir($newProjectDir, $newDir)
{
    echo " ".$newProjectDir.DIRECTORY_SEPARATOR.$newDir.'...';
    mkdir($newProjectDir.DIRECTORY_SEPARATOR.$newDir) or die("could not create directory\n");
    echo "done\n";
}

function startCopy($newProjectDir, $file)
{
    echo " ".$newProjectDir.DIRECTORY_SEPARATOR.$file.'...';
    copy(TONIC_DIR.DIRECTORY_SEPARATOR.'generate'.DIRECTORY_SEPARATOR.$file, $newProjectDir.DIRECTORY_SEPARATOR.$file) or die("could not copy file\n");
    echo "done\n";
}

define('TONIC_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'..');
$newProjectDir = $argv[1];
if (substr($newProjectDir, -1, 1) == DIRECTORY_SEPARATOR) {
	$newProjectDir = substr($newProjectDir, 0, -1);
}
echo "Creating new Tonic project at '".$newProjectDir."'\n";

if (is_dir($newProjectDir)) die("Project directory already exists, will not overwrite, exiting.\n");
echo "Creating project directory...";
mkdir($newProjectDir) or die("could not create directory\n");
echo "done\n";

echo "Creating project subdirectories...\n";
startMkdir($newProjectDir, 'lib');
startMkdir($newProjectDir, 'resources');
startMkdir($newProjectDir, 'scratch');

echo "Creating dispatcher...";
$fp = fopen($newProjectDir.DIRECTORY_SEPARATOR.'dispatch.php', 'w') or die("could not create file");
$dispatcher = file_get_contents(TONIC_DIR.DIRECTORY_SEPARATOR.'generate'.DIRECTORY_SEPARATOR.'dispatch.php');
$includePath = explode(PATH_SEPARATOR, get_include_path());
foreach ($includePath as $path) {
	if (substr(TONIC_DIR, 0, strlen($path)) == $path) {
		$tonicDir = substr(TONIC_DIR, strlen($path) + 1);
		break;
	}
}
if (isset($tonicDir)) {
	$dispatcher = str_replace('[TONIC]', $tonicDir, $dispatcher);
} else {
	$dispatcher = str_replace('[TONIC]', TONIC_DIR, $dispatcher);
}
fwrite($fp, $dispatcher);
fclose($fp);
echo "done\n";

echo "Creating .htaccess file...";
$fp = fopen($newProjectDir.DIRECTORY_SEPARATOR.'.htaccess', 'w') or die("could not create file");
fwrite($fp, '<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_URI} !dispatch\.php$
RewriteRule .* dispatch.php [L,QSA]
</IfModule>');
fclose($fp);
echo "done\n";

echo "Copying default Tonic resources...\n";
startCopy($newProjectDir.DIRECTORY_SEPARATOR.'resources', 'default.html');

echo "Finished.\n";

?>
