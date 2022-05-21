<div class="contain"></div>
<div id="reset-password-container">
    <h1>Mot de passe perdu</h1>
    <p>
        Entrez l'adresse mail liée à votre compte, nous vous enverrons un lien pour réinitialiser votre mot de passe
        à cette adresse si nous avons été en mesure de trouver votre compte.
    </p>
    <p>
        La réception du mail peut prendre plusieurs minutes, veillez à consulter vos spams si vous ne recevez pas le mail
    </p>
    <form action="/?c=user&a=reset-password-request" method="post">
        <div class="password-reset">
            <input type="email" name="email" id="email" minlength="5" maxlength="150" required>
            <small>Entrez l'adresse mail liée à votre compte</small>
        </div>
        <input type="submit" name="reset-password-send-email" value="Envoyer">
    </form>
</div>
