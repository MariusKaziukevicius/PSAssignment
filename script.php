<?php

require __DIR__.'/vendor/autoload.php';

use PayUzduotis\Controllers\BaseController;

$baseController = BaseController::makeSingleton();
$baseController->init();
$fees = $baseController->executeCommand("CalculateRemunerationFees");

if (is_array($fees) && !empty($fees))
{
    foreach ($fees as $fee)
    {
        echo $fee . "\n";
    }
}
