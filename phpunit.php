<?php

if (!is_dir(__DIR__ . '/vendor')) {
    die("You must install package dependencies locally (`composer install` from package root)\n");
}

include_once(__DIR__ . '/vendor/autoload.php');
