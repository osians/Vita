<?php

require __DIR__ . '/ConfigRepositoryInterface.php';
require __DIR__ . '/ConfigRepository.php';
require __DIR__ . '/Config.php';

$repository = new Vita\Core\Config\ConfigRepository();
$config = new Vita\Core\Config\Config($repository);

$config->set('dbport', '3306');
//var_dump($config->get('dbport'));
$config->save();