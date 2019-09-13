<?php

/* ---------------------------
 -- Vita brevis,
 -- ars longa,
 -- occasio praeceps,
 -- experimentum periculosum,
 -- iudicium difficile.
                 (Hipócrates)
 --------------------------- */

namespace Vita;

$ds = DIRECTORY_SEPARATOR;

require_once __DIR__ . "{$ds}..{$ds}vendor{$ds}autoload.php";

//    error Handler
error_reporting(E_ALL);

$environment = 'development';
$whoops = new \Whoops\Run;

if ($environment !== 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
}
else {
    $whoops->pushHandler(function($e){
        echo 'Todo: Friendly error page and send an email to the developer';
    });
}

$whoops->register();

echo 10 / 0;