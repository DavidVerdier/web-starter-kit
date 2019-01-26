<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Kernel\Container;

$container = new Container();

return $container->doctrineConsoleRunner();
