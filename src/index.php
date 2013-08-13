<?php

require_once "vendor/autoload.php";

$app = new \Symfony\Component\Console\Application();
$app->add(new XWiki\Command\InstallCommand());
$app->run();