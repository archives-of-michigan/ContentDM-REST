#!/usr/bin/php
<?php
/*
Tonic tools script
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

// $Id: tonic.php 14 2007-03-03 12:02:43Z peejeh $

// Check environment is ok
if (version_compare(phpversion(), "4.3.0", "<")) {
    die("PHP 4.3.0 or above is required, please upgrade.\n");
}

if ($argc < 3) {
    echo "Tonic project tools.\n";
    echo "Usage: ".$argv[0]." [start|serve] ".DIRECTORY_SEPARATOR."path".DIRECTORY_SEPARATOR."to".DIRECTORY_SEPARATOR."your".DIRECTORY_SEPARATOR."new".DIRECTORY_SEPARATOR."project\n";
} else {
	array_shift($argv);
    switch ($argv[0]) {
		case 'start':
			require 'tools'.DIRECTORY_SEPARATOR.'start.php';
			break;
		case 'serve':
			require 'tools'.DIRECTORY_SEPARATOR.'serve.php';
			break;
		default:
			die("Unknown option '".$argv[0]."'\n");
	}
}

?>
