<?php
// Mot de passe oublié
// Pattern email

use App\Router;
require __DIR__ . '/../include.php';

session_start();

try {
    Router::route();
}

catch (ReflectionException $e) {
    echo "La connexion a échoué";
}

