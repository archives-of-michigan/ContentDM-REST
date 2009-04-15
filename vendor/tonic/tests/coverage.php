<?php

define('PHPCOVERAGE_HOME', 'spikephpcoverage');
define('TONIC_HOME', 'C:\Documents and Settings\Paul\My Documents\Projects\tonic.tdd\trunk');

require_once PHPCOVERAGE_HOME.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'CoverageRecorder.php';
require_once PHPCOVERAGE_HOME.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'reporter'.DIRECTORY_SEPARATOR.'HtmlCoverageReporter.php';

$reporter = new HtmlCoverageReporter('Code Coverage Report', '', TONIC_HOME.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'coverage');
$cov = new CoverageRecorder(array(TONIC_HOME.DIRECTORY_SEPARATOR.'lib', TONIC_HOME.DIRECTORY_SEPARATOR.'adapters'), array(), $reporter);
$cov->startInstrumentation();

require TONIC_HOME.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'test.php';

$cov->stopInstrumentation();
$cov->generateReport();
$reporter->printTextSummary();

echo '<a href="file://'.TONIC_HOME.'/'.'tests'.'/'.'coverage'.'/'.'index.html'.'">'.TONIC_HOME.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'coverage'.'</a>';

?>
