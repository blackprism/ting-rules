<?php

use mageekguy\atoum\reports;
use mageekguy\atoum\writers\std;

$script->addDefaultReport();

// Currently this setting make php/xdebug segfault
// $script->enableBranchAndPathCoverage();

$telemetry = new reports\telemetry();
$telemetry->addWriter(new std\out());
$telemetry->readProjectNameFromComposerJson('composer.json');
$runner->addReport($telemetry);

$coverage = new reports\coverage\html();
$coverage->addWriter(new std\out());
$coverage->setOutPutDirectory('tests/coverage');
$runner->addReport($coverage);

$runner->addTestsFromDirectory('tests/units');
