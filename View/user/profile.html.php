<?php
use App\Model\Entity\User;?>


<?php

/* @var User $user */
$user = $data['profile'];
?>

<h1 id="title-profile"> Mon Profil</h1>

<div id="profile-container">
    <form action="/index.php?c=user&a=edit-user" method="post">
        <div id="container-profile">
            <div class="profile">
                <label for="firstname">Prénom</label>
                <input class="identity"  type="text" name="firstname" value="<?= $user->getFirstname() ?>">
            </div>
            <div>
                <label for="lastname">Nom</label>
                <input class="identity" type="text" name="lastname" value="<?= $user->getLastname() ?>">
            </div>
            <div>
                <label for="email">Email</label>
                <input class="identity" type="text" name="email" value="<?= $user->getEmail() ?>">
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input class="identity" type="password" name="password"">
                <small class="identity">NB: Laissez vide pour ne pas le changer</small>
            </div>

            <div>
                <label for="email">Répétez le mot de passe</label>
                <input class="identity" type="password" name="passwordRepeat">
            </div>
            <input type="submit" name="submit" value="Modifier" class="btn btn-secondary" id="submit valid">

        </div>

        <a href="/index.php?c=user&a=delete-user">Suppression du compte</a>
    </form>
</div>





