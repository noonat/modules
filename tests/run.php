<?php

require_once __DIR__.'/../lib/modules.php';
require_once __DIR__.'/../vendor/pecs/lib/pecs.php';

// include the tests
require __DIR__.'/modules/module.php';
require __DIR__.'/modules/loader.php';

// run 'em
\pecs\run();
