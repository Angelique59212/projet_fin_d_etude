<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Les Toubles Dys</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
    <div id="logout">
        <div>
            <i class="fas fa-bars" id="burger"></i>
        </div><?php

        use App\Model\Entity\User;

        if (!isset($_SESSION['user'])) {?>
            <a href="/?c=user&a=login">Login</a>/<a href="/?c=user&a=register">Inscription</a><?php
        }
        else {
            $user = $_SESSION['user'];
            /* @var User $user */ ?>
            <a href="/?c=user&a=show-user&id=<?= $user->getId() ?>">Mon profil</a>
            <a href="/?c=user&a=disconnect">Déconnexion</a><?php
        }
        ?>
    </div>
</header>

<h1 id="title-menu">Les troubles DYS</h1>

<div>
    <a href="/?c=home"><img id="logo" src="/assets/img/logo_dys.png" alt="learning_disabilities"></a>
</div>

<main><?= $html ?></main>

<footer>
    <div id="bottom">
        <a href="/?c=user&a=save-form">Contact</a>
        <a href="/?c=form&a=confidentiality">Confidentialité</a>
    </div>
</footer>

<script src="https://kit.fontawesome.com/6167e09880.js" crossorigin="anonymous"></script>
<script src="/assets/js/app.js"></script>

</body>
</html>