<?php

$kernel = AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'includePaths' => [__DIR__.'/../src'],
    'cacheDir'  => __DIR__ . '../../_data/cache',
]);

DG\BypassFinals::enable();
