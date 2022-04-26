<?php
use App\Model\Entity\User;?>

<?php
    if (isset($_SESSION['message'])) {?>
        <div class="message">
            <p><?= $_SESSION['message'] ?></p>
            <button id="close">x</button>
        </div>
        <?php
    }?>
<?php

/* @var User $user */
$user = $data['profile'];
?>

<h1 id="title-profile"> Mon Profil</h1>

<div id="profile-container">
    <form action="/index.php?c=user&a=edit-user&id=<?= $user->getId() ?>" method="post">
        <div id="container-profile">
            <div class="profile">
                <label for="firstname">Prénom</label>
                <input type="text" name="firstname" value="<?= $user->getFirstname() ?>">
            </div>
            <div>
                <label for="lastname">Nom</label>
                <input type="text" name="lastname" value="<?= $user->getLastname() ?>">
            </div>
            <div>
                <label for="email">Email</label>
                <input type="text" name="email" value="<?= $user->getEmail() ?>">
            </div>

            <input type="submit" name="submit" value="Modifier" class="btn btn-secondary" id="submit">

        </div>

        <a href="/index.php?c=user&a=delete-user&id=<?= $user->getId() ?>">Suppression du compte</a>
    </form>
</div>





