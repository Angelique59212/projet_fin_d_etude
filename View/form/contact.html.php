<?php
    if (isset($_SESSION['message'])) {?>
        <?php
        unset($_SESSION['message']);
    }?>

<div id="title-contact">
    <h1>Formulaire de Contact</h1>
</div>

<div id="container-contact" class="container">
    <div id="contact-container">
        <form action="/?c=user&a=save-form" method="post">
            <div>
                <label for="name"></label>
                <input type="text" id="name" name="name" placeholder="nom" required>

                <label for="mail"></label>
                <input type="email" name="mail" id="mail" placeholder="Entrez votre mail" required>

                <label for="message"></label>
                <textarea
                        name="message" id="message" cols="30" rows="10" minlength="20" maxlength="250" placeholder="Votre messsage" required>

                </textarea>

                <div>
                    <button id="btn-contact" class="btn btn-secondary" type="submit" name="submit">Envoyer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
if (isset($_SESSION['mail'])) {
    if ($_SESSION['mail'] === "mail-success") {?>
        <div class="message">
            <p>Votre message a bien été envoyé.</p>
            <button id="close">x</button>
        </div><?php
    }
    else {
        echo "Erreur lors de l'envoi";
    }
}
$_SESSION['mail'] = null;
