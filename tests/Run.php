<?php

require __DIR__.'/../vendor/autoload.php';
require_once('/../vendor/simpletest/simpletest/autorun.php');

foreach (glob(__DIR__."/*.php") as $filename)
{
    require_once($filename);
}
