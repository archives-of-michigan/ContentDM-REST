<?php

ini_set('max_execution_time', 0);

require_once 'simpletest'.DIRECTORY_SEPARATOR.'unit_tester.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'mock_objects.php';
require_once 'simpletest'.DIRECTORY_SEPARATOR.'reporter.php';

require_once 'tests'.DIRECTORY_SEPARATOR.'mockadapter.php';

$core = &new GroupTest('Core');
//*
$core->addTestFile('tests'.DIRECTORY_SEPARATOR.'request.php');
$core->addTestFile('tests'.DIRECTORY_SEPARATOR.'resource.php');
$core->addTestFile('tests'.DIRECTORY_SEPARATOR.'getresource.php');
$core->addTestFile('tests'.DIRECTORY_SEPARATOR.'putresource.php');
$core->addTestFile('tests'.DIRECTORY_SEPARATOR.'postresource.php');
$core->addTestFile('tests'.DIRECTORY_SEPARATOR.'deleteresource.php');
$core->addTestFile('tests'.DIRECTORY_SEPARATOR.'findresources.php');
//*/

$extras = &new GroupTest('Extras');
//*
$extras->addTestFile('tests'.DIRECTORY_SEPARATOR.'smartyresource.php');
//*/

$adapters = &new GroupTest('Adapters');
//*
$adapters->addTestFile('tests'.DIRECTORY_SEPARATOR.'fileadapter.php');
$adapters->addTestFile('tests'.DIRECTORY_SEPARATOR.'mysqladapter.php');
//*/

$test = &new GroupTest('Tonic');
$test->addTestCase($core);
$test->addTestCase($extras);
$test->addTestCase($adapters);

if (TextReporter::inCli()) {
	exit ($test->run(new TextReporter()) ? 0 : 1);
}
$test->run(new HtmlReporter());

?>