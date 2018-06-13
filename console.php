#!/usr/bin/env php

<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use GetYourGuide\MarketplaceTest\RetrieveProductCommand;

$application = new Application();
$application->add(new RetrieveProductCommand());

$application->run();
