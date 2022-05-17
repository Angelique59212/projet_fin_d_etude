<!doctype html>
<html lang="fr">
<head>
    <script src="/assets/js/tarteaucitron.js"></script>
    <script src="/assets/js/tarteaucitron/tarteaucitron.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Les Troubles Dys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="lib/jquery.js"></script>
</head>
<body>
<?php
if (isset($_SESSION['error'])) {?>
    <div class="message">
        <p><?= $_SESSION['error'] ?></p>
        <button id="close">x</button>
    </div>
    <?php
    unset($_SESSION['error']);
}
?>
<header>
    <div id="logout">
        <div>
            <i class="fas fa-bars" id="burger"></i>
        </div><?php

        use App\Model\Entity\User;

        if (!isset($_SESSION['user'])) {?>
            <a href="/?c=home">Home</a>
            <a href="/?c=user&a=login">Login</a>
            <a href="/?c=user&a=register">Inscription</a>
            <a href="/?c=article&a=list-article">Articles</a><?php
        }
        else {
            $user = $_SESSION['user'];
            /* @var User $user */ ?>
            <a href="/?c=home">Home</a>
            <a href="/?c=user&a=show-user&id=<?= $user->getId() ?>">Mon profil</a>
            <a href="/?c=article&a=list-article">Articles</a>
            <a href="/?c=user&a=disconnect">Déconnexion</a><?php
        }

        if (AbstractController::verifyRole()) {?>
            <a href="/index.php?c=article&a=add-article">Ajouter un article</a> <?php
        }
        ?>
    </div>
</header>

<div id="title-menu">
    <h1>Les troubles DYS</h1>
    <a href="/?c=home"><img id="logo" src="/assets/img/logo_dys.png" alt="learning_disabilities"></a>
</div>

<main><?= $html ?></main>

<footer>
    <div id="bottom">
        <a href="/?c=home">Home</a>
        <a href="/?c=user&a=save-form">Contact</a>
        <a href="/?c=confidentiality&a=confidentiality">Confidentialité</a>
        <p>&copy; Angélique Dehainaut</p>
    </div>
</footer>

<script src="https://kit.fontawesome.com/6167e09880.js" crossorigin="anonymous"></script>
 <!--Bootstrap JS bundle-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="/assets/js/app.js"></script>
<script
        src="https://cdn.tiny.cloud/1/go0ei34667b0j3tmim9m5wd5zfyaq0o0981rn9d13939fjx5/tinymce/6/tinymce.min.js"
        referrerpolicy="origin">
</script>
<script src="/assets/js/tiny.js"></script>
</body>
</html>