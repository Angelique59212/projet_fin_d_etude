<div class="contain"></div>
<div id="reset-password-container">
    <h1>Entrez votre nouveau mot de passe</h1>

    <form action="/?c=user&a=reset-password-set-new-password" method="post">
        <div class="password-reset">
            <div>
                <div>
                    <label for="password">Password: </label>
                    <input type="password" id="password" name="password" minlength="7" maxlength="70" required>
                </div>

                <div>
                    <label for="password-repeat">Répétez le mot de passe: </label>
                    <input type="password" id="password-repeat" name="password-repeat" required>
                </div>
            </div>
        </div>

        <small id="password-strength">Votre mot de passe doit contenir au moins: une majuscule, une minuscule, un chiffre, un caractère spécial</small>
        <input type="submit" name="submit-new-password" value="Envoyer">
    </form>
</div>
