<?php

use App\Router;
require __DIR__ . '/../includes.php';

session_start();

try {
    Router::route();
}

catch (ReflectionException $e) {
    echo "La connexion a échoué";
}