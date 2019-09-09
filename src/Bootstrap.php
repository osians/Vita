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

if ($environment !== 'production') {
    //    registra erros para o desenvolvedor na tela
}
else {
    //    Apresenta erros para Cliente mas envia detalhes para Desenvolvedor por email
}

